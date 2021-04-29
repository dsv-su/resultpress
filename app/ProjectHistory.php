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
        $keys = array_keys(array_merge($previous, $current));
        // dd($this->array_diff_assoc_recursive($current, $previous));
        $diff = array();
        foreach ($keys as $key) {
            if (!is_array($current[$key]) && $key != 'updated_at') {
                if ($previous[$key] != $current[$key]) {
                    $diff[$key] = array($previous[$key], $current[$key]);
                }
            }
        }
        if (key_exists('outcomes', $current)) {
            $outcomes_current = array_column($current['outcomes'], null, 'id');
        }
        if (key_exists('outcomes', $previous)) {
            $outcomes_previous = array_column($previous['outcomes'], null, 'id');
        }
       // dump($outcomes_current, $outcomes_previous);

        foreach ($outcomes_current as $id => $outcome) {
            if (isset($outcomes_previous[$id])) {
                if ($outcomes_previous[$id]['name'] != $outcome['name']) {
                    $diff['outcomes']['changed'][$id] = array($outcomes_previous[$id]['name'], $outcome['name']);
                }
                unset($outcomes_previous[$id]);
            } else {
                $diff['outcomes']['new'][$id] = $outcome['name'];
            }
        }
        if (!empty($outcomes_previous)) {
            foreach ($outcomes_previous as $id => $outcome) {
                $diff['outcomes']['deleted'][$id] = $outcome['name'];
            }
        }
        //dd($diff);
        return $diff;
    }
}
