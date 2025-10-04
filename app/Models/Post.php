<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'views_count',
        'is_featured',
        'meta',
        'user_id',
        'category_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'meta' => 'array',
        'views_count' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory(Builder $query, $categorySlug): Builder
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeByTag(Builder $query, $tagSlug): Builder
    {
        return $query->whereHas('tags', function ($q) use ($tagSlug) {
            $q->where('slug', $tagSlug);
        });
    }

    // Accessors & Mutators
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getExcerptAttribute($value): string
    {
        if (!empty($value)) {
            return $value;
        }

        return Str::limit(strip_tags($this->content), 150);
    }

    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200)); // Average reading speed: 200 words per minute
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            
            if ($post->status === 'published' && empty($post->published_at)) {
                $post->published_at = now();
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            
            if ($post->isDirty('status') && $post->status === 'published' && empty($post->published_at)) {
                $post->published_at = now();
            }
        });
    }
}
