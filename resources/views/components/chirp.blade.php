@props(['chirp', 'activeTopic' => null])

<div class="card bg-base-100 shadow">
    <div class="card-body">
        <div class="flex gap-3">
            @if ($chirp->user)
                <div class="avatar">
                    <div class="size-10 rounded-full">
                        <img src="https://avatars.laravel.cloud/{{ urlencode($chirp->user->email) }}"
                            alt="{{ $chirp->user->name }}'s avatar" class="rounded-full" />
                    </div>
                </div>
            @else
                <div class="avatar placeholder">
                    <div class="size-10 rounded-full">
                        <img src="https://avatars.laravel.cloud/f61123d5-0b27-434c-a4ae-c653c7fc9ed6?vibe=stealth"
                            alt="Anonymous User" class="rounded-full" />
                    </div>
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <div class="flex w-full justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-1">
                        <span class="text-sm font-semibold">{{ $chirp->user ? $chirp->user->name : 'Anonymous' }}</span>
                        <span class="text-base-content/60">&middot;</span>
                        <span class="text-sm text-base-content/60">{{ $chirp->created_at->diffForHumans() }}</span>
                        @if ($chirp->updated_at->gt($chirp->created_at->addSeconds(5)))
                            <span class="text-base-content/60">&middot;</span>
                            <span class="text-sm italic text-base-content/60">edited</span>
                        @endif
                    </div>

                    @can('update', $chirp)
                        <div class="flex gap-1">
                            <a href="{{ route('chirps.edit', $chirp) }}" class="btn btn-ghost btn-xs">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('chirps.destroy', $chirp) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this chirp?')"
                                    class="btn btn-ghost btn-xs text-error">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>

                @if ($chirp->topics() !== [])
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($chirp->topics() as $topic)
                            <a href="{{ route('home', ['topic' => $topic]) }}"
                                @class([
                                    'rounded-full border px-3 py-1 text-xs font-medium transition',
                                    'border-transparent bg-primary text-primary-content' => $activeTopic === $topic,
                                    'border-base-300 bg-base-100 text-base-content/70 hover:border-primary/30 hover:text-primary' => $activeTopic !== $topic,
                                ])>
                                #{{ $topic }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="mt-3 break-words text-[15px] leading-7 text-base-content/90">
                    {{ $chirp->linkedMessage($activeTopic) }}
                </div>
            </div>
        </div>
    </div>
</div>
