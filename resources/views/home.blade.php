<x-layout>
    <x-slot:title>
        Home Feed
    </x-slot:title>

    <div class="mx-auto max-w-6xl">
        <section class="mb-8 rounded-[2rem] border border-base-300/70 bg-base-100/85 px-6 py-8 shadow-sm backdrop-blur sm:px-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl space-y-4">
                    <div class="inline-flex items-center rounded-full border border-base-300 bg-base-100 px-3 py-1 text-xs uppercase tracking-[0.24em] text-base-content/60">
                        Topic-aware microfeed
                    </div>
                    <div class="space-y-3">
                        <h1 class="text-4xl leading-tight sm:text-5xl">Latest chirps, now grouped by what people are actually talking about.</h1>
                        <p class="max-w-xl text-sm leading-7 text-base-content/70 sm:text-base">
                            Post with hashtags like <span class="font-medium text-primary">#laravel</span>,
                            <span class="font-medium text-primary">#php</span> or
                            <span class="font-medium text-primary">#bootcamp</span> to turn the feed into a navigable topic stream.
                        </p>
                    </div>
                </div>

                @if ($activeTopic)
                    <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-primary/20 bg-primary/10 px-4 py-3">
                        <span class="text-xs font-medium uppercase tracking-[0.2em] text-primary/80">Active filter</span>
                        <span class="rounded-full bg-primary px-3 py-1 text-sm font-medium text-primary-content">
                            #{{ $activeTopic }}
                        </span>
                        <a href="{{ route('home') }}" class="btn btn-ghost btn-sm">Clear filter</a>
                    </div>
                @endif
            </div>
        </section>

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem]">
            <section class="space-y-6">
                @auth
                    <div class="card bg-base-100 shadow">
                        <div class="card-body gap-4">
                            <div class="flex flex-col gap-1">
                                <h2 class="text-xl">Share a new chirp</h2>
                                <p class="text-sm text-base-content/60">Hashtags become clickable topic links automatically.</p>
                            </div>

                            <form method="POST" action="{{ route('chirps.store') }}" class="space-y-4">
                                @csrf
                                <div class="form-control w-full">
                                    <textarea name="message" placeholder="What's on your mind? Try #laravel or #bootcamp"
                                        class="textarea textarea-bordered w-full resize-none @error('message') textarea-error @enderror" rows="4"
                                        maxlength="255" required>{{ old('message') }}</textarea>

                                    @error('message')
                                        <div class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <p class="text-xs uppercase tracking-[0.2em] text-base-content/50">
                                        Topic examples: #laravel #php #bootcamp
                                    </p>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Chirp
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card bg-base-100 shadow">
                        <div class="card-body flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="space-y-1">
                                <h2 class="text-xl">Join the conversation</h2>
                                <p class="text-sm text-base-content/60">Create an account to post chirps and start your own topic threads.</p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Sign In</a>
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Create Account</a>
                            </div>
                        </div>
                    </div>
                @endauth

                <div class="space-y-4">
                    @forelse ($chirps as $chirp)
                        <x-chirp :chirp="$chirp" :active-topic="$activeTopic" />
                    @empty
                        <div class="hero rounded-[2rem] border border-dashed border-base-300 py-12">
                            <div class="hero-content text-center">
                                <div>
                                    <svg class="mx-auto h-12 w-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                        </path>
                                    </svg>

                                    @if ($activeTopic)
                                        <p class="mt-4 text-base-content/60">No chirps found for #{{ $activeTopic }} yet.</p>
                                        <a href="{{ route('home') }}" class="btn btn-ghost btn-sm mt-4">Back to the full feed</a>
                                    @else
                                        <p class="mt-4 text-base-content/60">No chirps yet. Be the first to start a topic.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            <aside class="lg:sticky lg:top-6 lg:self-start">
                <div class="card bg-base-100 shadow">
                    <div class="card-body gap-4">
                        <div class="space-y-1">
                            <h2 class="text-lg">Trending topics</h2>
                            <p class="text-sm text-base-content/60">Based on the chirps currently visible in the feed.</p>
                        </div>

                        <div class="space-y-2">
                            @forelse ($trendingTopics as $topic => $count)
                                <a href="{{ route('home', ['topic' => $topic]) }}"
                                    @class([
                                        'flex items-center justify-between rounded-2xl border px-4 py-3 transition',
                                        'border-transparent bg-primary text-primary-content' => $activeTopic === $topic,
                                        'border-base-300 bg-base-100 hover:border-primary/30 hover:text-primary' => $activeTopic !== $topic,
                                    ])>
                                    <span class="font-medium">#{{ $topic }}</span>
                                    <span class="rounded-full border border-current/15 px-2 py-0.5 text-xs">{{ $count }}</span>
                                </a>
                            @empty
                                <p class="rounded-2xl border border-dashed border-base-300 px-4 py-5 text-sm text-base-content/60">
                                    Trending topics will show up as soon as chirps include hashtags.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-layout>
