<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\MorphTo;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'role_id', 'body', 'meta', 'commentable_id', 'commentable_type'];

    /**
     * Morph to the commentable model.
     * 
     * @return MorphTo
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the comment.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
