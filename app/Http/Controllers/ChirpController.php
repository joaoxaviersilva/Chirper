<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChirpRequest;
use App\Http\Requests\UpdateChirpRequest;
use App\Models\Chirp;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ChirpController extends Controller
{
    public function index(Request $request): View
    {
        $activeTopic = $this->normalizeTopic($request->string('topic')->toString());

        $chirps = Chirp::with('user')
            ->latest()
            ->limit(200)
            ->get()
            ->when(
                $activeTopic !== null,
                fn (Collection $chirps): Collection => $chirps->filter(
                    fn (Chirp $chirp): bool => $chirp->hasTopic($activeTopic)
                )
            )
            ->take(50)
            ->values();

        $trendingTopics = $chirps
            ->flatMap(fn (Chirp $chirp): array => $chirp->topics())
            ->countBy()
            ->sortDesc()
            ->take(5);

        return view('home', [
            'activeTopic' => $activeTopic,
            'chirps' => $chirps,
            'trendingTopics' => $trendingTopics,
        ]);
    }

    public function store(StoreChirpRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        $user->chirps()->create($validated);

        return redirect()->route('home')->with('success', 'Your chirp has been posted!');
    }

    public function edit(Chirp $chirp): View
    {
        $this->authorize('update', $chirp);

        return view('edit', compact('chirp'));
    }

    public function update(UpdateChirpRequest $request, Chirp $chirp): RedirectResponse
    {
        $chirp->update($request->validated());

        return redirect()->route('home')->with('success', 'Chirp updated!');
    }

    public function destroy(Chirp $chirp): RedirectResponse
    {
        $this->authorize('delete', $chirp);

        $chirp->delete();

        return redirect()->route('home')->with('success', 'Chirp deleted!');
    }

    private function normalizeTopic(string $topic): ?string
    {
        $normalizedTopic = Str::of($topic)
            ->trim()
            ->ltrim('#')
            ->lower()
            ->value();

        return preg_match('/^[a-z][a-z0-9_]*$/', $normalizedTopic) === 1
            ? $normalizedTopic
            : null;
    }
}
