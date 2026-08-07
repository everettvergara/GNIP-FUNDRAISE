@php
    $user = $campaign->user;
    $size = $size ?? 'sm';
    $showRole = $showRole ?? false;
    $avatarClass = $size === 'lg' ? 'w-12 h-12 text-base' : 'w-8 h-8 text-xs';
    $roleLine = null;

    if ($user && ($user->position || $user->organization)) {
        if ($user->position && $user->organization) {
            $roleLine = $user->position.' at '.$user->organization;
        } elseif ($user->position) {
            $roleLine = $user->position;
        } else {
            $roleLine = $user->organization;
        }
    }
@endphp

<div class="flex items-center gap-2 {{ $class ?? '' }}">
    @if ($user)
        @if ($user->hasPublicProfile())
            <a href="{{ route('fundraisers.show', $user) }}" class="flex items-center gap-2 group/organizer hover:text-gn-accent transition">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="{{ $avatarClass }} rounded-full object-cover border border-gray-200 shrink-0">
                @else
                    <div class="{{ $avatarClass }} rounded-full bg-gn-accent/20 flex items-center justify-center text-gn-accent font-semibold shrink-0">
                        {{ $user->initials }}
                    </div>
                @endif
                <div class="min-w-0">
                    <span class="text-sm text-gn-text/70 group-hover/organizer:text-gn-accent block">
                        @if ($label ?? false)
                            {{ $label }} {{ $user->full_name }}
                        @else
                            {{ $user->full_name }}
                        @endif
                    </span>
                    @if ($showRole && $roleLine)
                        <span class="text-xs text-gn-text/50 block mt-0.5 group-hover/organizer:text-gn-accent/80">{{ $roleLine }}</span>
                    @endif
                </div>
            </a>
        @else
            <div class="flex items-center gap-2">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="{{ $avatarClass }} rounded-full object-cover border border-gray-200 shrink-0">
                @else
                    <div class="{{ $avatarClass }} rounded-full bg-gn-accent/20 flex items-center justify-center text-gn-accent font-semibold shrink-0">
                        {{ $user->initials }}
                    </div>
                @endif
                <div class="min-w-0">
                    <span class="text-sm text-gn-text/70 block">
                        @if ($label ?? false)
                            {{ $label }} {{ $user->full_name }}
                        @else
                            {{ $user->full_name }}
                        @endif
                    </span>
                    @if ($showRole && $roleLine)
                        <span class="text-xs text-gn-text/50 block mt-0.5">{{ $roleLine }}</span>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="flex items-center gap-2">
            <div class="{{ $avatarClass }} rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-semibold shrink-0">
                ?
            </div>
            <span class="text-sm text-gn-text/70">{{ $label ?? false ? $label.' Organizer' : 'Organizer' }}</span>
        </div>
    @endif
</div>
