<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\faqs;

class FactoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //User::factory()->count(500)->create();
        
        faqs::factory()->create([
            'title' => 'What is JAOM Connect?',
            'definition' => '"JAOM Connect" is a comprehensive web app designed to help members of the JAOM community connect with one another, engage in discussions about faith and spirituality, stay informed about the latest updates from the organization, and view upcoming community activities on a shared calendar. The app provides a centralized hub for community members to stay connected and engaged with one another, fostering a sense of belonging and community.',
        ]);

        faqs::factory()->create([
            'title' => 'Who founded JAOM and what is their passion?',
            'definition' => 'JAOM, an organization with a deep commitment to serving God and His people, was founded by Pastor Gretchen C. Jaos. With an unwavering passion and dedication, Pastor Gretchen C. Jaos established JAOM to fulfill his mission of making a positive impact on the lives of others through the love and teachings of God.',
        ]);

        faqs::factory()->create([
            'title' => 'Why donating us?',
            'definition' => 'Donating to JAOM provides an opportunity for community members and supporters to contribute to the progress and development of the JAOM community. You would be considered one who helps us strengthen our relationship.',
        ]);

        faqs::factory()->create([
            'title' => 'What are our missions in our community?',
            'definition' => 'Jesus ALPHA-OMEGA Ministry in Tanjay City, Negros Oriental is committed to sharing the love of Jesus Christ through evangelism, discipleship, and community outreach. Our mission is to help people develop a deeper relationship with God, grow in their faith, and live out the gospel in their daily lives. We strive to be a light in our community, sharing hope and joy with those around us.',
        ]);

        faqs::factory()->create([
            'title' => 'What are the core values of JAOM?',
            'definition' => 'The core values of JAOM include faith, love, service, integrity, and excellence. These values guide the community in all of its activities and programs.',
        ]);

        faqs::factory()->create([
            'title' => 'How can I become a member of JAOM?',
            'definition' => 'To become a member of JAOM, you can attend one of the community\'s services or programs and express your interest to one of the leaders. You will then be guided on the steps to become an official member.',
        ]);

        faqs::factory()->create([
            'title' => 'Does JAOM offer programs for children and youth?',
            'definition' => 'Yes, JAOM offers various programs for children and youth, such as Sunday school, youth fellowship, and outreach programs that cater to the needs of children and young people.',
        ]);

        faqs::factory()->create([
            'title' => 'Can I volunteer at JAOM?',
            'definition' => 'Yes, JAOM welcomes volunteers who are willing to serve and participate in the community\'s programs and ministries. You can inquire with the leaders on how you can volunteer and contribute to the community.',
        ]);

        faqs::factory()->create([
            'title' => 'What are the regular activities in JAOM community?',
            'definition' => 'JAOM community has regular Sunday worship services, prayer meetings, Bible studies, and outreach programs in the local community.',
        ]);

        faqs::factory()->create([
            'title' => 'How can I get involved in JAOM community?',
            'definition' => 'You can get involved in JAOM community by attending their worship services and other activities, joining a small group or ministry team, and volunteering for outreach programs.',
        ]);

        faqs::factory()->create([
            'title' => 'What is the vision of JAOM community?',
            'definition' => 'The vision of JAOM community is to be a Christ-centered, disciple-making community that transforms lives and impacts the local community and beyond through the power of the Holy Spirit.',
        ]);
    }
}
