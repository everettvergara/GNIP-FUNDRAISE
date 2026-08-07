<section class="max-w-2xl mt-8 space-y-4">
    <div class="bg-white p-6 rounded-lg border border-gray-200">
        <h2 class="text-lg font-semibold">Delete Campaign</h2>
        <p class="mt-1 text-sm text-gn-text/70">
            Permanently remove this campaign. This action cannot be undone.
        </p>

        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-campaign-deletion')"
            class="mt-4 px-4 py-2 bg-gn-orange text-white text-sm font-semibold rounded-md hover:opacity-90"
        >
            Delete Campaign
        </button>
    </div>

    <x-modal name="confirm-campaign-deletion" focusable>
        <form method="POST" action="{{ route('campaigns.destroy', $campaign->slug) }}" class="p-6">
            @csrf
            @method('DELETE')

            <h2 class="text-lg font-semibold text-gray-900">
                Delete this campaign?
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                This will permanently delete <strong>{{ $campaign->title }}</strong> and all of its images. This action cannot be undone.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50"
                >
                    Cancel
                </button>
                <x-danger-button>
                    Delete Campaign
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
