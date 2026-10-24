<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class NewsArticle extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'news_articles';

    protected $fillable = [
        'title',
        'summary',
        'content',
        'image_url',
        'source_name',
        'source_url',
        'category',
        'status', // 'pending', 'approved', 'rejected'
        'is_featured',
        'published_at',
        'moderated_by',
        'moderated_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
        'moderated_at' => 'datetime',
    ];

    /**
     * Người kiểm duyệt bài báo
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
