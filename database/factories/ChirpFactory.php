<?php

namespace Database\Factories;

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Chirp>
 */
class ChirpFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'message' => fake()->sentence(10),
        ];
    }

    public function withTopic(string $topic): static
    {
        $normalizedTopic = Str::of($topic)
            ->trim()
            ->ltrim('#')
            ->lower()
            ->value();

        return $this->state(fn (): array => [
            'message' => fake()->sentence(6).' #'.$normalizedTopic,
        ]);
    }
}
