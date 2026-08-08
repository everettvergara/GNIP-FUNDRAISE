<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'key' => 'verification',
                'subject' => 'Verify your Good Neighbors Philippines account',
                'body' => "Greeting {{ name }},\n\nThank you for your interest in supporting Good Neighbor's Fundraising A. To complete your account creation and get your campaign ready to accept donations, please use the following link:\n\n{{ verification_url }}\n\nPlease note that in order to have your campaign able to accept donations, the organization you requested to support will need to confirm your campaign after you are done creating it.\n\nHappy fundraising!\n\nGood Neighbors",
            ],
            [
                'key' => 'password_reset',
                'subject' => 'Reset your password',
                'body' => "Hello {{ name }},\n\nYou requested a password reset. Click the link below to set a new password:\n\n{{ reset_url }}\n\nIf you did not request this, you can ignore this email.",
            ],
            [
                'key' => 'campaign_published',
                'subject' => 'Your campaign is ready to share',
                'body' => "Hello {{ name }},\n\nYour campaign \"{{ campaign_title }}\" has been approved and is now live.\n\nShare it with your network: {{ campaign_url }}\n\nThank you for fundraising with Good Neighbors Philippines.",
            ],
            [
                'key' => 'campaign_rejected',
                'subject' => 'Your campaign needs changes',
                'body' => "Hello {{ name }},\n\nYour campaign \"{{ campaign_title }}\" was not approved at this time.\n\nReason: {{ rejection_reason }}\n\nPlease review the feedback, update your campaign and documents, then submit again for approval:\n{{ edit_url }}\n\nThank you for fundraising with Good Neighbors Philippines.",
            ],
            [
                'key' => 'campaign_revoked',
                'subject' => 'Your campaign has been revoked',
                'body' => "Hello {{ name }},\n\nYour campaign \"{{ campaign_title }}\" has been removed from public listing by our team.\n\nReason: {{ revocation_reason }}\n\nView your campaign: {{ edit_url }}\n\nIf you have questions, please contact Good Neighbors Philippines.",
            ],
            [
                'key' => 'campaign_submitted_admin',
                'subject' => 'New campaign submitted for approval',
                'body' => "A campaign has been submitted for your review.\n\nCampaign: {{ campaign_title }}\nFundraiser: {{ fundraiser_name }} ({{ fundraiser_email }})\nSubmitted at: {{ submitted_at }}\n\nReview the campaign: {{ admin_review_url }}",
            ],
            [
                'key' => 'donation_receipt',
                'subject' => 'Thank you for your donation',
                'body' => "Dear {{ donor_name }},\n\nThank you for giving us the opportunity to support this meaningful cause. We hope our small contribution helps make a positive impact in the lives of those who need it most.\n\nDonation amount: PHP {{ amount }}\nCampaign: {{ campaign_title }}\n\nWishing your organization continued success in all that you do.",
            ],
            [
                'key' => 'donation_received',
                'subject' => 'You received a new donation',
                'body' => "Hello {{ fundraiser_name }},\n\nGreat news! {{ donor_name }} donated PHP {{ amount }} to your campaign \"{{ campaign_title }}\".\n\nHappy to support this meaningful cause. Keep up the great work!\n\nView your campaign: {{ campaign_url }}",
            ],
            [
                'key' => 'share_by_email',
                'subject' => 'Support this meaningful cause',
                'body' => "Hello,\n\nI am fundraising for a cause I care about and would love your support.\n\n{{ campaign_title }}\n{{ campaign_url }}\n\nThank you for considering a donation.",
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::query()->updateOrCreate(
                ['key' => $template['key']],
                $template,
            );
        }
    }
}
