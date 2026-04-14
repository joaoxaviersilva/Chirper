<?php

use App\Models\Chirp;
use App\Models\User;

it('shows the home feed without a filter', function () {
    Chirp::factory()->count(3)->create();

    $response = $this->get(route('home'));

    $response
        ->assertSuccessful()
        ->assertSee('Latest chirps')
        ->assertSee('Trending topics');
});

it('filters the feed by topic case-insensitively', function () {
    Chirp::factory()->create(['message' => 'Shipping a new feature for #Laravel today']);
    Chirp::factory()->create(['message' => 'Styling the UI with #tailwind']);

    $response = $this->get(route('home', ['topic' => 'LaRaVeL']));

    $response
        ->assertSuccessful()
        ->assertSee('#laravel')
        ->assertSee('Shipping a new feature for')
        ->assertDontSee('Styling the UI with');
});

it('renders an empty state when a topic has no chirps', function () {
    Chirp::factory()->create(['message' => 'Talking about #php today']);

    $response = $this->get(route('home', ['topic' => 'laravel']));

    $response
        ->assertSuccessful()
        ->assertSee('No chirps found for #laravel yet.');
});

it('renders hashtags as topic links in chirp messages', function () {
    Chirp::factory()->create(['message' => 'Working through the #bootcamp milestone']);

    $response = $this->get(route('home'));

    $response
        ->assertSuccessful()
        ->assertSee(route('home', ['topic' => 'bootcamp']), false)
        ->assertSee('#bootcamp');
});

it('allows an authenticated user to create, update, and delete chirps', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('chirps.store'), ['message' => 'First post for #laravel'])
        ->assertRedirect(route('home'));

    $chirp = Chirp::query()->firstOrFail();

    expect($chirp->message)->toBe('First post for #laravel');

    $this->actingAs($user)
        ->put(route('chirps.update', $chirp), ['message' => 'Updated post for #php'])
        ->assertRedirect(route('home'));

    expect($chirp->fresh()->message)->toBe('Updated post for #php');

    $this->actingAs($user)
        ->delete(route('chirps.destroy', $chirp))
        ->assertRedirect(route('home'));

    $this->assertDatabaseMissing('chirps', ['id' => $chirp->id]);
});
