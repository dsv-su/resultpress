<?php

namespace App\Http\Controllers;

use App\Activity;
use App\ActivityUpdate;
use App\Output;
use App\OutputUpdate;
use App\Project;
use App\ProjectUpdate;
use Illuminate\Http\Request;

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
     * @param  Project  $project
     *
     */
    public function show(Project $project)
    {
        return view('project.show', [
            'project' => $project,
            'activities' => Activity::where('project_id', $project->id)->get(),
            'outputs' => Output::where('project_id', $project->id)->get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Project  $project
     *
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
     * @param  \Illuminate\Http\Request  $request
     * @param  Project  $project
     *
     */
    public function update(Project $project)
    {
        $validatedAttributes = request()->validate([
            'project_name' => 'required',
            'project_description' => 'required',
        ]);

        $project->name = request('project_name');
        $project->description = request('project_description');
        $project->status = request('project_status');
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


        /*
        return view('project.update', [
            'project' => $project,
            'activities' => Activity::where('project_id', $project->id)->get(),
            'outputs' => Output::where('project_id', $project->id)->get()
        ]);
        */
    }

    public function save_update(Project $project)
    {
        $projectupdate = new ProjectUpdate(array('project_id' => $project->id, 'summary' => request('project_update_summary') ?? null));
        $projectupdate->save();
        $projectupdate_id = $projectupdate->id;

        $activity_update_array['id'] = request('activity_update_id') ?? null;
        $activity_update_array['activity_id'] = request('activity_id');
        $activity_update_array['comment'] = request('activity_comment');
        $activity_update_array['status'] = request('activity_status');
        $activity_update_array['money'] = request('activity_money');
        $activity_update_array['date'] = request('activity_date');

        if (!empty($activity_update_array['id'])) {
            foreach ($activity_update_array['id'] as $key => $id) {
                $activityupdate = new ActivityUpdate;
                $activityupdate->activity_id = Activity::findOrFail($activity_update_array['activity_id'][$key])->id;
                $activityupdate->comment = $activity_update_array['comment'][$key];
                $activityupdate->status = $activity_update_array['status'][$key];
                $activityupdate->money = $activity_update_array['money'][$key];
                $activityupdate->date = $activity_update_array['date'][$key];
                $activityupdate->project_update_id = $projectupdate_id;
                $activityupdate->save();
            }
        }

        $output_update_array['id'] = request('output_update_id') ?? null;
        $output_update_array['output_id'] = request('output_id');
        $output_update_array['value'] = request('output_value');

        if (!empty($output_update_array['id'])) {
            foreach ($output_update_array['id'] as $key => $id) {
                $outputupdate = new OutputUpdate();
                $outputupdate->output_id = Output::findOrFail($output_update_array['output_id'][$key])->id;
                $outputupdate->value = $output_update_array['value'][$key];
                $outputupdate->project_update_id = $projectupdate_id;
                $outputupdate->save();
            }
        }

        return redirect()->route('projectupdate_show', $projectupdate_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
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
        $activities = Activity::where('project_id', $project->id)->forcedelete();
        $outputs = Output::where('project_id', $project->id)->forcedelete();

        // Delete project
        $project->delete();
        return redirect()->route('home');
    }
}
