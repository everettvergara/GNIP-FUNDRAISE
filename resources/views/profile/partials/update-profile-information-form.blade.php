<section>
    <header>
        <h2 class="text-lg font-medium">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gn-text/70">
            {{ __('Update your profile photo, bio, and account details. Your public profile is visible by default for transparency.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label class="block text-sm font-medium mb-2">Profile Photo</label>
            <div class="flex items-center gap-4">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="" class="w-16 h-16 rounded-full object-cover border border-gray-200">
                @else
                    <div class="w-16 h-16 rounded-full bg-gn-accent/20 flex items-center justify-center text-gn-accent font-semibold text-lg">
                        {{ $user->initials }}
                    </div>
                @endif
                <input id="avatar" name="avatar" type="file" accept="image/*"
                       class="block w-full text-sm text-gn-text/70 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gn-accent/10 file:text-gn-accent hover:file:bg-gn-accent/20">
            </div>
            @error('avatar')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="first_name" class="block text-sm font-medium mb-1">First Name</label>
            <input id="first_name" name="first_name" type="text" class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent"
                   value="{{ old('first_name', $user->first_name) }}" required autofocus autocomplete="given-name">
            @error('first_name')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="last_name" class="block text-sm font-medium mb-1">Last Name</label>
            <input id="last_name" name="last_name" type="text" class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent"
                   value="{{ old('last_name', $user->last_name) }}" required autocomplete="family-name">
            @error('last_name')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="organization" class="block text-sm font-medium mb-1">Organization</label>
            <input id="organization" name="organization" type="text" class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent"
                   value="{{ old('organization', $user->organization) }}" autocomplete="organization">
            @error('organization')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="position" class="block text-sm font-medium mb-1">Position</label>
            <input id="position" name="position" type="text" class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent"
                   value="{{ old('position', $user->position) }}" autocomplete="organization-title">
            @error('position')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="about_me" class="block text-sm font-medium mb-1">About Me</label>
            <textarea id="about_me" name="about_me" rows="4"
                      class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">{{ old('about_me', $user->about_me) }}</textarea>
            @error('about_me')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input id="email" name="email" type="email" class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gn-accent hover:opacity-80">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-gn-accent">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-start gap-3">
            @if ($user->isCampaignUser())
                <input type="hidden" name="is_profile_public" value="0">
                <input id="is_profile_public" name="is_profile_public" type="checkbox" value="1"
                       class="mt-1 rounded border-gray-300 text-gn-accent focus:ring-gn-accent"
                       {{ old('is_profile_public', $user->is_profile_public) ? 'checked' : '' }}>
                <div>
                    <label for="is_profile_public" class="text-sm font-medium">Public profile</label>
                    <p class="text-sm text-gn-text/70 mt-1">
                        Allow others to view your fundraiser profile page and link to it from your campaigns. Enabled by default for transparency.
                    </p>
                </div>
            @else
                <div>
                    <p class="text-sm font-medium">Public profile</p>
                    <p class="text-sm text-gn-text/70 mt-1">
                        Public fundraiser profiles are only available for active campaign user accounts.
                    </p>
                </div>
            @endif
        </div>
        @error('is_profile_public')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror

        <div class="flex items-center gap-4">
            <button type="submit" class="px-4 py-2 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gn-text/70"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
