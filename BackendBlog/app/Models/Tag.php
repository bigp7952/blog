<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'articles_count',
    ];

    protected $casts = [
        'articles_count' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get the articles for the tag.
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tags');
    }

    /**
     * Scope a query to only include popular tags.
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('articles_count', 'desc')->limit($limit);
    }
}
