<?php

namespace App\Http\Controllers;

use App\Outcome;
use App\Output;
use App\OutputUpdate;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OutcomeController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $outcome = Outcome::find($id);
        $outcome->summary = $request->summary;
        $outcome->completed_on = Carbon::now();
        $outcome->user_id = \Auth::user()->id;
        $outcome->completed = 1;
        $output_updates = array();
        foreach ($request->outcome_outputs as $output) {
            $output_id = Output::findorfail($output)->id;
            $output_updates[$output_id]['indicator'] = Output::find($output_id)->indicator ?? 0;
            $output_updates[$output_id]['value'] = OutputUpdate::where('output_id', $output_id)->get()->last()->value ?? 0;
        }
        $outcome->outputs = json_encode($output_updates);
        $outcome->save();
        return redirect()->route('project_show', $request->project);
    }
}
