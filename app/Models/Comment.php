<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Policies\CommentPolicy;
use Illuminate\Support\Facades\Gate;

class Comment extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Comment::class => CommentPolicy::class,
    ];

    protected $fillable = [
        'user_id',
        'post_id',
        'content',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the formatted time difference
     *
     * @return string
     */
    public function getFormattedTime()
    {
        $diff = now()->diffInMinutes($this->updated_at);
        
        if ($diff < 1) {
            return 'just now';
        } elseif ($diff < 60) {
            return "$diff " . ($diff === 1 ? 'min' : 'mins') . ' ago';
        } elseif ($diff < 1440) {
            $hours = floor($diff / 60);
            return "$hours " . ($hours === 1 ? 'hr' : 'hrs') . ' ago';
        } else {
            $days = floor($diff / 1440);
            return "$days " . ($days === 1 ? 'day' : 'days') . ' ago';
        }
    }
}
