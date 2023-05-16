<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutputUpdate extends Model
{
    protected $fillable = ['project_update_id', 'output_id', 'value', 'progress'];

    /**
     * Get the output that owns the OutputUpdate
     *
     * @return BelongsTo
     */
    public function output(): BelongsTo
    {
        return $this->belongsTo(Output::class);
    }

    /**
     * Get the project_update that owns the OutputUpdate
     *
     * @return BelongsTo
     */
    public function project_update(): BelongsTo
    {
        return $this->belongsTo(ProjectUpdate::class);
    }

    /**
     * Get the aggregated outputs corresponding to this output update
     *
     * @return bool
     */
    public function getAggregated() {
        if ($this->output->target != 'aggregated') {
            $output_id = $this->output->id;
            $return = [];
            foreach ($this->output->project->aggregated_outputs() as $ao) {
                $ids = json_decode($ao->target);
                if (in_array($output_id, $ids)) {
                    $return[] = $ao;
                }
            }
            return $return ?? null;
        }
        return false;
    }
}
