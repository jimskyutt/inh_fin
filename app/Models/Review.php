<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_id',
        'homeowner_id',
        'service_provider_id',
        'service_id',
        'homeowner_name',
        'service_provider_name',
        'service_name',
        'rating',
        'review',
        'job_title',
        'location',
        'scheduled_date',
        'status',
        'admin_feedback',
        'reviewed_by',
        'reviewed_at',
        'images',
        'deleted_by_owner',
        'deleted_by_provider',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('notDeleted', function (Builder $builder) {
            $builder->where('deleted_by_owner', 0);
        });
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'scheduled_date' => 'date',
        'reviewed_at' => 'datetime',
        'rating' => 'integer',
        'images' => 'array',
    ];

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if the review is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the review is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the review is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
