<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swaggest\JsonDiff\JsonDiff;
use Swaggest\JsonDiff\JsonPatch;
use Swaggest\JsonDiff\JsonValueReplace;

class ProjectHistory extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'user_id', 'data'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diff()
    {
        $previous = json_decode($this->project->history()->orderBy('id', 'desc')->where('id', '<', $this->id)->first()->data);
        $current = json_decode($this->data);
        $diff = new JsonDiff($previous, $current, JsonDiff::COLLECT_MODIFIED_DIFF);
        return $diff;
    }
}
