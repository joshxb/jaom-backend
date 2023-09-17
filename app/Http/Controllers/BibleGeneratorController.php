<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\BibleQuoteMail;
use App\Models\User;
use Carbon\Carbon;
use Djunehor\Logos\Bible;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;

class BibleGeneratorController extends Controller
{
    // Maximum number of retries in case of an error
    private $maxRetries = 3;

    public function generateBibleQuote($retryCount = 0)
    {
        try {
            $bibleBooks = [
                'Genesis',
                'Joshua',
                'Judges',
                'Ruth',
                'Ezra',
                'Nehemiah',
                'Esther',
                'Psalms',
                'Proverbs',
                'Isaiah',
                'Jeremiah',
                'Daniel',
                'Hosea',
                'Joel',
                'Amos',
                'Obadiah',
                'Jonah',
                'Micah',
                'Nahum',
                'Zephaniah',
                'Haggai',
                'Zechariah',
                'Malachi',
                'Matthew',
                'Mark',
                'John',
                'Romans',
                'Galatians',
                'Ephesians',
                'Philippians',
                'Philemon',
                'Hebrews',
                'James',
                'Jude',
                'Revelation',
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

            $limit = 100;
            $usersToSend = $users;

            foreach ($usersToSend as $user) {
                Mail::to($user->email)->send(new BibleQuoteMail($bibleEmailData));
            }

            $notification = new Notification();
            $notification->title = 'Newly Bible Quote Sent to Email';
            $notification->notification_object = json_encode([
                'todo_id' => null,
                'title' => 'Hello there, bible-quote was successfully sent to respective emails based on scheduled time:',
                'content' => $verse . ': ' .$quote,
            ]);
            $notification->user_id = auth()->user()->id;
            $notification->save();

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
