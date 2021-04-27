<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutcomeUpdate extends Model
{
    protected $dates = ['completed_on'];
    protected $fillable = ['project_update_id', 'outcome_id', 'completed_on', 'summary', 'outputs'];

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(Outcome::class);
    }

    public function project_update(): BelongsTo
    {
        return $this->belongsTo(ProjectUpdate::class);
    }

    public function calculateOutputValue($output_id) {
        $value = 0;
        foreach (OutputUpdate::where('output_id', $output_id)->get() as $ou) {
            if ($ou->project_update->status == 'approved' && $ou->project_update->created_at->lte($this->created_at)) {
                $value += $ou->value;
            }
        }
        return $value;
    }
}
