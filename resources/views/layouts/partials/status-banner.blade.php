@if (session('status'))
    <div class="bg-gn-accent/10 border-b border-gn-accent/20">
        <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-gn-text">
            @if (session('status') === 'donation-pending')
                Thank you! Your donation is being processed. You will be redirected to our payment partner soon.
            @elseif (session('status') === 'profile-updated')
                Profile updated successfully.
            @elseif (session('status') === 'password-updated')
                Password updated successfully.
            @elseif (session('status') === 'campaign-created')
                Campaign created! You can continue editing before publishing.
            @elseif (session('status') === 'campaign-updated')
                Campaign updated successfully.
            @elseif (session('status') === 'campaign-deleted')
                Campaign deleted successfully.
            @elseif (session('status') === 'campaign-submitted-for-approval')
                Your campaign has been submitted for admin approval.
            @elseif (session('status') === 'campaign-document-uploaded')
                Document uploaded successfully.
            @elseif (session('status') === 'campaign-document-removed')
                Document removed successfully.
            @elseif (session('status') === 'impact-report-created')
                Impact report posted successfully.
            @elseif (session('status') === 'impact-report-deleted')
                Impact report deleted successfully.
            @else
                {{ session('status') }}
            @endif
        </div>
    </div>
@endif
