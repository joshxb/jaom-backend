<?php

namespace App\Response\Manager\web;

use App\Assets\BibleQuote;
use App\Mail\BibleQuoteMail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;

class BibleGeneratorResponse
{
    private $maxRetries = 3;

    private $bibleQuotes;

    public function __construct(BibleQuote $bibleQuotes)
    {
        $this->bibleQuotes = $bibleQuotes;
    }

    public function generateBibleQuote($retryCount = 0)
    {
        try {
            $users = User::whereNotNull("email_verified_at")->get()->shuffle();
            $currentDay = Carbon::now()->format('l');

            $images = [
                'https://github.com/joshxb/joam-project-images/blob/main/337482704_1399156404249401_3014320667708945884_n.jpg?raw=true',
                'https://github.com/joshxb/joam-project-images/blob/main/337600841_1224255664889243_5242546871589928112_n.jpg?raw=true',
                'https://github.com/joshxb/joam-project-images/blob/main/337664640_998857938189489_2265375645851121647_n.jpg?raw=true',
                'https://github.com/joshxb/joam-project-images/blob/main/JAOM4.jpg?raw=true',
                'https://github.com/joshxb/joam-project-images/blob/main/JAOM13.jpg?raw=true',
                'https://github.com/joshxb/joam-project-images/blob/main/JAOM1.jpg?raw=true',
                'https://github.com/joshxb/joam-project-images/blob/main/JAOM2.jpg?raw=true',
                'https://github.com/joshxb/joam-project-images/blob/main/JAOM3.jpg?raw=true',
                'https://github.com/joshxb/joam-project-images/blob/main/JAOM6.jpg?raw=true',
                'https://github.com/joshxb/joam-project-images/blob/main/JAOM14.jpg?raw=true'
            ];

            shuffle($images);
            $randomImages = array_slice(array_unique($images), 0, 3);

            $data = $this->bibleQuotes->getQuote();
            $randomIndex = array_rand($data);
            $randomQuote = $data[$randomIndex]['text'];
            $randomVerse = $data[$randomIndex]['verse'];

            $bibleEmailData = [
                'verse' => $randomVerse,
                'quote' => $randomQuote,
                'day' => $currentDay,
                'message' => $this->getRandomMessage($currentDay),
                'randomImages' => $randomImages,
            ];

            $usersToSend = $users;
            $recipientEmails = $usersToSend->pluck('email')->toArray();

            Mail::bcc($recipientEmails)->send(new BibleQuoteMail($bibleEmailData));

            $notification = new Notification();
            $notification->title = 'Newly Bible Quote Sent to Email';
            $notification->notification_object = json_encode([
                'todo_id' => null,
                'title' => 'Hello there, bible-quote was successfully sent to respective emails based on scheduled time:',
                'content' => $randomVerse . ': ' . $randomQuote,
            ]);
            $notification->user_id = auth()->user()->id;
            $notification->save();
        } catch (Exception $e) {
            if ($retryCount < $this->maxRetries) {
                // leave empty code
            } else {
                return response()->json(['error' => 'An error occurred while generating the Bible quote.'], 301);
            }
        }
    }

    public function getRandomMessage($day)
    {
        $messages = [
            "Esteemed citizens, on this delightful " . $day . ", the Ministry shares a message of positivity with a quote designed to uplift your spirits.",
            "Greetings to our wonderful community! As we embrace " . $day . ", the Ministry is excited to share a motivating quote to add a touch of inspiration to your day.",
            "Good day, esteemed community members! " . $day . " is here, and the Ministry has curated a thoughtful quote to inspire reflection and positivity among us.",
            "Greetings, dear community! On this " . $day . ", the Ministry presents a carefully selected quote to inspire contemplation and foster a sense of unity.",
            "Hello community members! How's your " . $day . " going? The Ministry has a thought-provoking quote to share, encouraging a moment of reflection.",
            "Greetings to our wonderful community! As " . $day . " unfolds, the Ministry presents an enriching quote to stimulate thought and enhance our collective experience.",
            "Respected community members, as we navigate through " . $day . ", the Ministry extends warm regards. May you find inspiration in the motivational quote we share.",
            "Greetings to our esteemed community! As " . $day . " unfolds, we offer you a carefully selected quote designed to bring inspiration and a renewed sense of community.",
            "Salutations, valued community members! On this " . $day . ", the Ministry extends greetings and shares a motivational quote aimed at fostering a sense of community.",
            "Greetings to our wonderful community! As " . $day . " unfolds, the Ministry presents you with an enlightening quote, carefully curated to stimulate community spirit and positivity.",
        ];

        $randomIndex = array_rand($messages);

        return $messages[$randomIndex];
    }
}
