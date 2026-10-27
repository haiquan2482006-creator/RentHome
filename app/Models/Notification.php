<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Notification extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'notifications';

    protected $fillable = [
        'id',
        'data',
        'read_at',
        'user_id',
        'role',
        'target_id',
        'post_id',
        'building_id',
        'action_url',
        'title',
        'message',
        'type', // success, error, warning, info, building
        'is_read',
        'category',
        'changes'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    protected $appends = [
        'title',
        'message',
        'action_url',
        'type',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!empty($model->data) && is_array($model->data)) {
                if (empty($model->title) && isset($model->data['title'])) {
                    $model->title = $model->data['title'];
                }
                if (empty($model->message) && isset($model->data['message'])) {
                    $model->message = $model->data['message'];
                }
                if (empty($model->action_url) && isset($model->data['action_url'])) {
                    $model->action_url = $model->data['action_url'];
                }
                if (empty($model->post_id) && isset($model->data['post_id'])) {
                    $model->post_id = $model->data['post_id'];
                }
                if (empty($model->category) && isset($model->data['category'])) {
                    $model->category = $model->data['category'];
                }
                if (empty($model->type) && isset($model->data['type'])) {
                    $model->type = $model->data['type'];
                }
            }
            if ($model->is_read === null) {
                $model->is_read = !empty($model->read_at);
            }
        });
    }

    public function getTitleAttribute($value = null)
    {
        return $value ?: ($this->attributes['title'] ?? ($this->data['title'] ?? 'Thông báo hệ thống'));
    }

    public function getMessageAttribute($value = null)
    {
        return $value ?: ($this->attributes['message'] ?? ($this->data['message'] ?? ''));
    }

    public function getActionUrlAttribute($value = null)
    {
        return $value ?: ($this->attributes['action_url'] ?? ($this->data['action_url'] ?? url('/thong-bao-ca-nhan')));
    }

    public function getTypeAttribute($value = null)
    {
        $raw = $value ?: ($this->attributes['type'] ?? null);
        if (!empty($raw) && !str_contains((string)$raw, '\\')) {
            return $raw;
        }
        return $this->data['type'] ?? ($this->data['category'] ?? ($this->attributes['category'] ?? 'info'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }
}
