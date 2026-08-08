@props([
    'url',
    'title' => '',
])

<button
    type="button"
    x-data="{
        copied: false,
        async shareCampaign() {
            const payload = { title: @js($title), url: @js($url) };

            if (navigator.share) {
                try {
                    await navigator.share(payload);
                    return;
                } catch (error) {
                    if (error?.name === 'AbortError') {
                        return;
                    }
                }
            }

            try {
                await navigator.clipboard.writeText(payload.url);
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            } catch (error) {
                window.prompt('Copy this campaign link:', payload.url);
            }
        },
    }"
    x-on:click="shareCampaign()"
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-6 py-3 border border-gn-accent text-gn-accent font-semibold rounded-md hover:bg-gn-accent/10 whitespace-nowrap transition']) }}
>
    <span x-show="!copied">Share</span>
    <span x-show="copied" x-cloak>Link copied!</span>
</button>
