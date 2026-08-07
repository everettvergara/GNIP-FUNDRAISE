<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Fund Raising Main Page',
                'slug' => 'home',
                'meta_title' => 'Good Neighbors Philippines Fundraising',
                'meta_description' => 'Create and support meaningful fundraising campaigns with Good Neighbors Philippines.',
                'body' => [
                    'hero' => [
                        'heading' => 'Fundraise for Good',
                        'subheading' => 'Start a campaign, share your story, and rally support for causes that matter.',
                        'cta_primary' => 'I want to fundraise',
                        'cta_secondary' => 'Browse Campaigns',
                    ],
                    'footer' => [
                        'copyright' => 'Good Neighbors Philippines © 2025 All Rights Reserved',
                    ],
                ],
            ],
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'meta_title' => 'Frequently Asked Questions',
                'meta_description' => 'Answers to common questions about fundraising and donating with Good Neighbors Philippines.',
                'body' => [
                    'sections' => [
                        [
                            'question' => 'How do I start a fundraising campaign?',
                            'answer' => 'Register for an account, verify your email, and create your campaign from your dashboard. Once submitted, our team will review it before it goes live.',
                        ],
                        [
                            'question' => 'How can I donate to a campaign?',
                            'answer' => 'Visit any active campaign page and click Donate Now. You will be redirected to our secure payment partner to complete your contribution.',
                        ],
                        [
                            'question' => 'What payment methods are accepted?',
                            'answer' => 'We accept GCash, Maya, bank transfers, and major credit and debit cards through our payment partner.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Terms of Use',
                'slug' => 'terms-of-use',
                'meta_title' => 'Terms of Use',
                'meta_description' => 'Terms governing use of the Good Neighbors Philippines fundraising platform.',
                'body' => [
                    'content' => 'By using this website and fundraising platform, you agree to use the service responsibly and in accordance with applicable laws. Good Neighbors Philippines reserves the right to update these terms at any time.',
                ],
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'meta_title' => 'Terms and Conditions',
                'meta_description' => 'Terms and conditions for fundraisers and donors.',
                'body' => [
                    'content' => 'These terms and conditions apply to all fundraising campaigns hosted on this platform. Campaign owners are responsible for the accuracy of their campaign information and for using donated funds in line with stated campaign goals.',
                ],
            ],
            [
                'title' => 'Data Privacy Policy',
                'slug' => 'privacy-policy',
                'meta_title' => 'Data Privacy Policy',
                'meta_description' => 'How Good Neighbors Philippines collects, uses, and protects your personal data.',
                'body' => [
                    'content' => 'Good Neighbors Philippines is committed to protecting your personal information. We collect only the data necessary to operate the fundraising platform, process donations, and communicate with you. We handle your data according to current Data Protection laws.',
                ],
            ],
            [
                'title' => 'Donor Policy',
                'slug' => 'donor-policy',
                'meta_title' => 'Donor Policy',
                'meta_description' => 'Policies and guidelines for donors using the Good Neighbors Philippines platform.',
                'body' => [
                    'content' => 'Donations made through this platform support the campaigns you choose. Receipts are issued for completed transactions. Refund requests are handled according to our internal policies and applicable regulations.',
                ],
            ],
            [
                'title' => 'Support Resources',
                'slug' => 'support',
                'meta_title' => 'Support Resources',
                'meta_description' => 'Get help with your fundraising campaign or donation.',
                'body' => [
                    'content' => 'Need assistance? Contact our support team for help with account access, campaign setup, donations, and technical issues.',
                ],
            ],
            [
                'title' => 'Fundraising Tips and Best Practices',
                'slug' => 'fundraising-tips',
                'meta_title' => 'Fundraising Tips and Best Practices',
                'meta_description' => 'Tips to help you run a successful fundraising campaign.',
                'body' => [
                    'sections' => [
                        [
                            'heading' => 'Tell a compelling story',
                            'content' => 'Description text here. You can talk about the work your organization does, and why donations to your campaign are so important. This is the place where you can touch donors hearts and souls. You can upload videos and images as well.',
                        ],
                        [
                            'heading' => 'Share your campaign',
                            'content' => 'Use social media, email, and personal networks to spread the word. Regular updates keep supporters engaged and motivated to give.',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($pages as $page) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'body' => $page['body'],
                    'meta_title' => $page['meta_title'],
                    'meta_description' => $page['meta_description'],
                    'is_published' => true,
                ],
            );
        }
    }
}
