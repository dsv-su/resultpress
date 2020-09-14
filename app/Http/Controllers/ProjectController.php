<?php

namespace App\Http\Controllers;

use App\Activity;
use App\ActivityUpdate;
use App\File;
use App\Output;
use App\OutputUpdate;
use App\Project;
use App\ProjectUpdate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $projects = Project::latest()->get();
        return view('project.index', ['projects' => $projects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
    }

    /**
     * Display the specified resource.
     *
     * @param Project $project
     *
     * @return Application|Factory|View
     */
    public function show(Project $project)
    {
        $project_updates = ProjectUpdate::where('project_id', $project->id)->where('project_updates.approved', 1)->get();
        $activities = Activity::where('project_id', $project->id)->get();
        $outputs = Output::where('project_id', $project->id)->get();
        $moneyspent = 0;
        $budget = 0;
        foreach ($activities as $a) {
            $activityupdates = ActivityUpdate::where('activity_id', $a->id)
                ->join('project_updates', 'project_update_id', '=', 'project_updates.id')
                ->where('project_updates.approved', 1)
                ->orderBy('date', 'asc')
                ->get();

            $comments = array();
            foreach ($activityupdates as $au) {
                $moneyspent += $au->money;
                $puindex = 0;
                foreach ($project_updates as $index => $pu) {
                    if ($pu->id == $au->project_update_id) {
                        $puindex = $index + 1;
                    }
                }
                $comments[$puindex] = $au->comment;
            }

            ksort($comments);
            $a->comments = $comments;
            $budget += $a->budget;

            $latestupdate = $activityupdates->last();
            if ($latestupdate) {
                $a->status = $latestupdate->status;
                switch ($a->status) {
                    case 3:
                        $a->statusdate = 'on ' . $latestupdate->date->format('d/m/Y');
                        break;
                    case 0:
                        $a->statusdate = '';
                        break;
                    default:
                        $a->statusdate = $latestupdate->date ? 'since ' . $latestupdate->date->format('d/m/Y') : '';
                        break;
                }
            }
        }
        foreach ($outputs as $o) {
            $outputupdates = OutputUpdate::where('output_id', $o->id)
                ->join('project_updates', 'project_update_id', '=', 'project_updates.id')
                ->where('project_updates.approved', 1)
                ->get();
            $valuesum = 0;
            foreach ($outputupdates as $ou) {
                $valuesum += $ou->value;
            }
            $o->valuesum = $valuesum;
            if ($valuesum == 0) {
                $o->valuestatus = 2;
            } elseif ($valuesum >= $o->target) {
                $o->valuestatus = 3;
            } else {
                $o->valuestatus = 1;
            }
        }
        $project->dates = $this->project_dates($project);
        $project->projectstart = !$activities->isEmpty() ? Activity::where('project_id', $project->id)->orderBy('start', 'asc')->first()->start->format('d/m/Y') : null;
        $project->projectend = !$activities->isEmpty() ? Activity::where('project_id', $project->id)->orderBy('end', 'desc')->first()->end->format('d/m/Y') : null;
        $project->updatesnumber = count($project_updates);
        $project->recentupdate = !$project_updates->isEmpty() ? $project_updates->sortBy('created_at')->values()->last()->created_at->format('d/m/Y') : null;
        $project->moneyspent = $moneyspent;
        $project->budget = $budget;

        return view('project.show', ['project' => $project, 'activities' => $activities, 'outputs' => $outputs]);
    }

    public function project_dates(Project $project) {
        if ($project->start) {
            if ($project->end) {
                $dates = $project->start->format('d/m/Y') . " — " . $project->end->format('d/m/Y');
            } else {
                $dates = $project->start->format('d/m/Y') . " — not set";
            }
        } else {
            $dates = 'Not set';
        }
        return $dates;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Project $project
     *
     * @return Application|Factory|View
     */
    public function edit(Project $project)
    {
        return view('project.form', [
            'project' => $project,
            'activities' => Activity::where('project_id', $project->id)->get(),
            'outputs' => Output::where('project_id', $project->id)->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Project $project
     *
     * @return RedirectResponse
     */
    public function update(Project $project)
    {
        request()->validate([
            'project_name' => 'required',
            'project_description' => 'required',
        ]);

        $project->name = request('project_name');
        $project->description = request('project_description');
        $project->start = request('project_start') ?? null;
        $project->status = 0; // temp value
        $project->end = request('project_end') ?? null;
        $project->activities = is_array(request('activity_id')) ? 1 : 0;
        $project->save();

        //Activities
        //Request from form --> this should later be refactored
        $activity_array['id'] = request('activity_id') ?? null;
        $activity_array['name'] = request('activity_name');
        $activity_array['description'] = request('activity_description') ?? null;
        $activity_array['start'] = request('activity_start');
        $activity_array['end'] = request('activity_end');
        $activity_array['name'] = request('activity_name');
        $activity_array['budget'] = request('activity_budget');

        // Outputs
        $output_array['id'] = request('output_id') ?? null;
        $output_array['indicator'] = request('output_indicator');
        $output_array['target'] = request('output_target');

        // Remove deleted activities
        foreach (Activity::where('project_id', $project->id)->get() as $a) {
            if (!$activity_array['id'] || !in_array($a->id, $activity_array['id'])) {
                Activity::findOrFail($a->id)->delete();
            }
        }

        if (!empty($activity_array['id'])) {
            foreach ($activity_array['id'] as $key => $id) {
                $data = array();
                $data['title'] = $activity_array['name'][$key];
                $data['description'] = $activity_array['description'][$key];
                $data['start'] = $activity_array['start'][$key];
                $data['end'] = $activity_array['end'][$key];
                $data['budget'] = $activity_array['budget'][$key];
                $data['project_id'] = $project->id;
                if ($id) {
                    Activity::where('id', $id)->update($data);
                } else {
                    Activity::create($data);
                }
            }
        }

        // Remove deleted outputs
        foreach (Output::where('project_id', $project->id)->get() as $o) {
            if (!$output_array['id'] || !in_array($o->id, $output_array['id'])) {
                Output::findOrFail($o->id)->delete();
            }
        }

        if (!empty($output_array['id'])) {
            foreach ($output_array['id'] as $key => $id) {
                $data = array();
                $data['indicator'] = $output_array['indicator'][$key];
                $data['target'] = $output_array['target'][$key];
                $data['project_id'] = $project->id;
                if ($id) {
                    Output::where('id', $id)->update($data);
                } else {
                    Output::create($data);
                }
            }
        }

        // -->

        return redirect()->route('project_show', $project);
    }


    public function write_update(Project $project)
    {
        return view('project.update', ['project' => $project]);
    }

    public function save_update(Project $project)
    {
        $projectupdate = new ProjectUpdate(array('project_id' => $project->id, 'summary' => request('project_update_summary') ?? null));
        $projectupdate->save();
        $projectupdate_id = $projectupdate->id;

        // Process activity updates
        $activity_update_array['activity_id'] = request('activity_id');
        $activity_update_array['comment'] = request('activity_comment');
        $activity_update_array['status'] = request('activity_status');
        $activity_update_array['money'] = request('activity_money');
        $activity_update_array['date'] = request('activity_date') ?? null;

        foreach ($activity_update_array['activity_id'] as $key => $id) {
            $activityupdate = new ActivityUpdate;
            $activityupdate->activity_id = Activity::findOrFail($id)->id;
            $activityupdate->comment = $activity_update_array['comment'][$key];
            $activityupdate->status = $activity_update_array['status'][$key];
            $activityupdate->money = $activity_update_array['money'][$key];
            $activityupdate->date = $activity_update_array['date'][$key];
            $activityupdate->project_update_id = $projectupdate_id;
            $activityupdate->save();
        }

        // Process output updates
        $output_update_array['output_id'] = request('output_id');
        $output_update_array['value'] = request('output_value');

        foreach ($output_update_array['output_id'] as $key => $id) {
            if (!is_numeric($output_update_array['output_id'][$key])) {
                //Create new output since it's an unexpected one
                $data = array();
                $data['indicator'] = $output_update_array['output_id'][$key];
                $data['target'] = 0;
                $data['project_id'] = $project->id;
                $id = Output::create($data)->id;
            }
            $outputupdate = new OutputUpdate();
            $outputupdate->output_id = Output::findOrFail($id)->id;
            $outputupdate->value = $output_update_array['value'][$key];
            $outputupdate->project_update_id = $projectupdate_id;
            $outputupdate->save();
        }

        // Update file reference
        $file_ids = request('file_id') ?? null;
        if ($file_ids) {
            if (!is_array($file_ids)) {
                $file_ids = array($file_ids);
            }
            foreach ($file_ids as $file_id) {
                $file = File::findOrFail($file_id);
                $file->itemid = $projectupdate_id;
                $file->save();
            }
        }

        return redirect()->route('projectupdate_show', $projectupdate_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Project $project
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Project $project)
    {
        // Delete associated updates
        $project_updates = ProjectUpdate::where('project_id', $project->id)->get();
        foreach ($project_updates as $pu) {
            ActivityUpdate::where('project_update_id', $pu->id)->delete();
            OutputUpdate::where('project_update_id', $pu->id)->delete();
            ProjectUpdate::destroy($pu->id);
        }

        // Delete outputs and activities
        Activity::where('project_id', $project->id)->forcedelete();
        Output::where('project_id', $project->id)->forcedelete();

        // Delete project
        $project->delete();
        return redirect()->route('home');
    }
}
