<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\BibleQuoteMail;
use App\Models\User;
use Carbon\Carbon;
use Djunehor\Logos\Bible;
use Exception;
use Illuminate\Support\Facades\Mail;

class BibleGeneratorController extends Controller
{
    // Maximum number of retries in case of an error
    private $maxRetries = 3;

    public function generateBibleQuote($retryCount = 0)
    {
        try {
            $bibleBooks = [
                'Genesis', 'Exodus', 'Leviticus', 'Numbers', 'Deuteronomy', 'Joshua', 'Judges', 'Ruth', 'Ezra', 'Nehemiah', 'Esther', 'Job', 'Psalms', 'Proverbs',
                'Ecclesiastes', 'Song of Solomon', 'Isaiah', 'Jeremiah', 'Lamentations', 'Ezekiel', 'Daniel', 'Hosea', 'Joel',
                'Amos', 'Obadiah', 'Jonah', 'Micah', 'Nahum', 'Habakkuk', 'Zephaniah', 'Haggai', 'Zechariah', 'Malachi', 'Matthew',
                'Mark', 'Luke', 'John', 'Acts', 'Romans', 'Galatians', 'Ephesians', 'Philippians',
                'Colossians', 'Titus', 'Philemon', 'Hebrews', 'James',
                'Jude', 'Revelation',
            ];

            // Get a random book from the array
            $randomBook = $bibleBooks[array_rand($bibleBooks)];

            // Initialize the Bible API
            $bible = new Bible();

            // Get the Book of the selected book
            $bible->book($randomBook);
            $bookData = $bible->getBook();

            // Get the total number of chapters in the book
            $totalChapters = count($bookData['chapters']);

            // Get a random chapter number
            $randomChapterNumber = rand(1, $totalChapters);

            // Set the random chapter
            $bible->chapter($randomChapterNumber);
            $chapterData = $bible->getChapter();

            // Get all the verses in the chapter
            $verses = $chapterData['verses'];

            // Get a random verse number
            $randomVerseNumber = array_rand($verses);
            $quote = $verses[$randomVerseNumber]['text'];

            // Build the complete quote with book, chapter, and verse
            $verse = "{$randomBook} {$randomChapterNumber}:{$randomVerseNumber}";

            $users = User::whereNotNull("email_verified_at")->get()->shuffle();

            $currentDay = Carbon::now()->format('l');

            $bibleEmailData = [
                'verse' => $verse,
                'quote' => $quote,
                'day' => $currentDay,
            ];

            $limit = 50;
            $usersToSend = $users->take($limit);

            foreach ($usersToSend as $user) {
                Mail::to($user->email)->send(new BibleQuoteMail($bibleEmailData));
            }

            // Return the random verse along with the complete quote
            return [
                'verse' => $verse,
                'quote' => $quote,
                'day' => $currentDay,
            ];
        } catch (Exception $e) {
            if ($retryCount < $this->maxRetries) {
                // Retry the function with an incremented retry count
                return $this->generateBibleQuote($retryCount + 1);
            } else {
                // Maximum retries reached, return an error response
                return response()->json(['error' => 'An error occurred while generating the Bible quote.'], 500);
            }
        }

    }
}
