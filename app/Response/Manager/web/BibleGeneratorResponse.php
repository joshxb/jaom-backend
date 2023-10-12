<?php

namespace App\Response\Manager\web;

use App\Mail\BibleQuoteMail;
use App\Models\User;
use Carbon\Carbon;
use Djunehor\Logos\Bible;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;

class BibleGeneratorResponse
{
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

            $randomBook = $bibleBooks[array_rand($bibleBooks)];
            $bible = new Bible();

            $bible->book($randomBook);
            $bookData = $bible->getBook();
            $totalChapters = count($bookData['chapters']);
            $randomChapterNumber = rand(1, $totalChapters);

            $bible->chapter($randomChapterNumber);
            $chapterData = $bible->getChapter();

            $verses = $chapterData['verses'];
            $randomVerseNumber = array_rand($verses);
            $quote = $verses[$randomVerseNumber]['text'];
            $verse = "{$randomBook} {$randomChapterNumber}:{$randomVerseNumber}";

            $users = User::whereNotNull("email_verified_at")->get()->shuffle();
            $currentDay = Carbon::now()->format('l');

            $bibleEmailData = [
                'verse' => $verse,
                'quote' => $quote,
                'day' => $currentDay,
            ];

            $usersToSend = $users;
            $recipientEmails = $usersToSend->pluck('email')->toArray();

            Mail::bcc($recipientEmails)->send(new BibleQuoteMail($bibleEmailData));

            $notification = new Notification();
            $notification->title = 'Newly Bible Quote Sent to Email';
            $notification->notification_object = json_encode([
                'todo_id' => null,
                'title' => 'Hello there, bible-quote was successfully sent to respective emails based on scheduled time:',
                'content' => $verse . ': ' . $quote,
            ]);
            $notification->user_id = auth()->user()->id;
            $notification->save();

            $jsonPath = public_path('json/bible-quotes.json');
            $jsonContent = file_get_contents($jsonPath);
            $data = json_decode($jsonContent, true)['quotes'];

            $randomIndex = array_rand($data);
            $randomQuote = $data[$randomIndex]['text'];
            $randomVerse = $data[$randomIndex]['verse'];

            return [
                'verse' => ($this->returnZeroOrOne() == 0 || $this->returnZeroOrOne() == 1 ) ? $randomVerse : $verse,
                'quote' => ($this->returnZeroOrOne() == 0 || $this->returnZeroOrOne() == 1 ) ? $randomQuote : $quote,
                'day' => $currentDay,
            ];
        } catch (Exception $e) {
            if ($retryCount < $this->maxRetries) {
                return $this->generateBibleQuote($retryCount + 1);
            } else {
                return response()->json(['error' => 'An error occurred while generating the Bible quote.'], 500);
            }
        }
    }

    private function returnZeroOrOne()
    {
        $randomNumber = rand(0, 2);
        return $randomNumber;
    }
}
