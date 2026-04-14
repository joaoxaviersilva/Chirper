<?php

namespace App\Models;

use Database\Factories\ChirpFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Chirp extends Model
{
    /** @use HasFactory<ChirpFactory> */
    use HasFactory;

    protected $fillable = [
        'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return list<string>
     */
    public static function extractTopics(string $message): array
    {
        preg_match_all('/(?<![A-Za-z0-9_])#([A-Za-z][A-Za-z0-9_]*)/', $message, $matches);

        return array_values(array_unique(array_map(
            fn (string $topic): string => Str::lower($topic),
            $matches[1] ?? []
        )));
    }

    /**
     * @return list<string>
     */
    public function topics(): array
    {
        return self::extractTopics($this->message);
    }

    public function hasTopic(string $topic): bool
    {
        $normalizedTopic = self::normalizeTopic($topic);

        if ($normalizedTopic === null) {
            return false;
        }

        return in_array($normalizedTopic, $this->topics(), true);
    }

    public function linkedMessage(?string $activeTopic = null): HtmlString
    {
        preg_match_all(
            '/(?<![A-Za-z0-9_])#([A-Za-z][A-Za-z0-9_]*)/',
            $this->message,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        $html = '';
        $cursor = 0;
        $normalizedActiveTopic = $activeTopic !== null ? self::normalizeTopic($activeTopic) : null;

        foreach ($matches[0] as $index => [$hashtag, $offset]) {
            $topic = self::normalizeTopic($matches[1][$index][0]);

            if ($topic === null) {
                continue;
            }

            $html .= e(substr($this->message, $cursor, $offset - $cursor));
            $html .= sprintf(
                '<a href="%s" class="%s">%s</a>',
                e(route('home', ['topic' => $topic])),
                e($topic === $normalizedActiveTopic
                    ? 'font-semibold text-primary underline decoration-primary/40 underline-offset-4'
                    : 'font-medium text-primary underline decoration-primary/30 underline-offset-4 transition hover:decoration-primary hover:text-primary'),
                e($hashtag),
            );

            $cursor = $offset + strlen($hashtag);
        }

        $html .= e(substr($this->message, $cursor));

        return new HtmlString(nl2br($html, false));
    }

    public function scopeForTopic(Builder $query, string $topic): void
    {
        $normalizedTopic = self::normalizeTopic($topic);

        if ($normalizedTopic === null) {
            return;
        }

        $query->whereRaw('lower(message) like ?', ['%#'.$normalizedTopic.'%']);
    }

    private static function normalizeTopic(string $topic): ?string
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
