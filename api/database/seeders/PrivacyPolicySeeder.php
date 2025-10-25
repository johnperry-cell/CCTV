<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\PrivacyPrompt;
use App\Models\Policy;
use App\Models\Privacy;

class PrivacyPolicySeeder extends seeder {
    use WithoutModelEvents;

    public function run() {
        $now = now();

        $prompt =
            "<p class=\"my-3 first:mt-0 last:mb-0\">We use cookies on our website to give you the most relevant experience by remembering your preferences and repeat visits. By clicking <strong>\"Accept All\"</strong>, you consent to the use of ALL the cookies. You may visit also visit our Privacy Policy page for more details.</p>";

        $policies = [
            [
                "collapsible" => false,
                "order" => 1,
                "title" => null,
                "content" =>
                    '<p class=\"my-3 first:mt-0 last:mb-0\">Welcome to this <b>Website</b>! This privacy policy outlines how we collect, use, and protect your personal information when you use our services.</p>',
            ],
            [
                "collapsible" => true,
                "order" => 2,
                "title" => "1. Information We Collect",
                "content" =>
                    '<ul><li><p class=\"my-3 first:mt-0 last:mb-0\"><strong>Authentication Data</strong><br>When you use our website, we collect information necessary for authentication purposes. This may include your username, password, and any other details required to verify your identity.</p></li><li><p class=\"my-3 first:mt-0 last:mb-0\"><strong>Settings Information</strong><br>We may use cookies to store certain settings preferences that enhance your user experience. These settings may include language preferences, display preferences, or other customization options.</p></li></ul>',
            ],
            [
                "collapsible" => true,
                "order" => 3,
                "title" => "2. How We Use Cookies",
                "content" =>
                    '<p class=\"my-3 first:mt-0 last:mb-0\">Cookies are small text files stored on your device that help us provide a better user experience. Our website uses cookies for the sole purpose of authentication and storing settings preferences. These cookies are essential for the proper functioning of the website.</p>',
            ],
            [
                "collapsible" => true,
                "order" => 4,
                "title" => "3. Third-Party Cookies",
                "content" =>
                    '<p class=\"my-3 first:mt-0 last:mb-0\">We do not use third-party cookies for tracking or advertising purposes. The cookies employed on our website are strictly limited to authentication and settings storage.</p>',
            ],
            [
                "collapsible" => true,
                "order" => 5,
                "title" => "4. Information Security",
                "content" =>
                    '<p class=\"my-3 first:mt-0 last:mb-0\">We take appropriate measures to safeguard your personal information from unauthorized access, disclosure, alteration, and destruction. We regularly review and enhance our security procedures to ensure the integrity and confidentiality of your data.</p>',
            ],
            [
                "collapsible" => true,
                "order" => 6,
                "title" => "5. Data Retention",
                "content" =>
                    '<p class=\"my-3 first:mt-0 last:mb-0\">We retain your authentication data and settings information for as long as necessary to fulfill the purposes outlined in this privacy policy. If you have concerns about the retention of your data, please contact us using the information provided below.</p>',
            ],
            [
                "collapsible" => true,
                "order" => 7,
                "title" => "6. Your Rights",
                "content" =>
                    '<p class=\"my-3 first:mt-0 last:mb-0\">You have the right to access, update, or delete your personal information. If you have any concerns about your data, please contact us at <strong>cgob.mitd@gmail.com</strong>.</p>',
            ],
            [
                "collapsible" => true,
                "order" => 8,
                "title" => "7. Changes to this Privacy Policy",
                "content" =>
                    '<p class=\"my-3 first:mt-0 last:mb-0\">We may update this privacy policy from time to time. Any changes will be posted on this page, and the date of the last update will be revised accordingly.</p>',
            ],
            [
                "collapsible" => true,
                "order" => 9,
                "title" => "8. Contact Us",
                "content" =>
                    '<p class=\"my-3 first:mt-0 last:mb-0\">If you have any questions or concerns about this privacy policy, please contact us at <strong>cgob.mitd@gmail.com</strong>.</p>',
            ],
            [
                "collapsible" => false,
                "order" => 10,
                "title" => null,
                "content" =>
                    '<p class=\"my-3 first:mt-0 last:mb-0\">By using our website, you agree to the terms outlined in this privacy policy.</p>',
            ],
        ];

        $promptModel = PrivacyPrompt::create([
            "content" => $prompt,
        ]);

        $attach = [];

        foreach ($policies as $policy) {
            $tmp = Policy::create([
                "title" => $policy["title"],
                "content" => $policy["content"],
            ]);
            $attach[$tmp->id] = [
                "collapsible" => $policy["collapsible"],
                "order" => $policy["order"],
            ];
        }

        $privacy = Privacy::create([
            "user_id" => 1,
            "prompt_id" => $promptModel->id,
            "activated_at" => $now,
        ]);

        $privacy->policies()->attach($attach);
    }
}
