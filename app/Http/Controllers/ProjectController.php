<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
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
     *
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function store(Activity $activity)
    {

        request()->validate([
            'project_name' => 'required',
            'project_description' => 'required'
        ]);

        $project = Project::create([
            'name' => request('project_name'),
            'description' => request('project_description'),
            'status' => request('project_status'),
            'activities' => request('activities'),
            'outputs' => 0,
            'aggregated_outputs' => 0
        ]);

        //Activities

        if(request('activities') == 1) {
            //Request from form --> this should later be refactored
            $activity_array['name'] = request('activity_name');
            $activity_array['start'] = request('activity_start');
            $activity_array['end'] = request('activity_end');
            $activity_array['name'] = request('activity_name');
            $activity_array['budget'] = request('activity_budget');
            //
            $n = 0;
            $added_activities = count($activity_array['name']);
            $added_activities = $added_activities - $n;
            $y = $n;
            for ($x = 0; $x < $added_activities; $x++) {
                $data['title'] = $activity_array['name'][$y + $x];
                $data['start'] = $activity_array['start'][$y + $x];
                $data['end'] = $activity_array['end'][$y + $x];
                $data['budget'] = $activity_array['budget'][$y + $x];
                $data['project_id'] = $project->id;

                $activity->create($data);
            }
        }
        // -->
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     */
    public function show(Project $project)
    {
        return view('project.show', ['project' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     */
    public function edit(Project $project)
    {
        return view('project.form', ['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     *
     */
    public function update(Project $project, Activity $activity)
    {
        request()->validate([
            'project_name' => 'required',
            'project_description' => 'required'
        ]);

        $project->name = request('project_name');
        $project->description = request('project_description');
        $project->status = request('project_status');
        $project->activities = request('activities');
        $project->update();

        //Activities
        if(request('activities') == 1) {
            //Request from form --> this should later be refactored
            $activity_array['name'] = request('activity_name');
            $activity_array['start'] = request('activity_start');
            $activity_array['end'] = request('activity_end');
            $activity_array['name'] = request('activity_name');
            $activity_array['budget'] = request('activity_budget');
            //
            $n = 0;
            $added_activities = count($activity_array['name']);
            $added_activities = $added_activities - $n;
            $y = $n;
            for ($x = 0; $x < $added_activities; $x++) {
                $data['title'] = $activity_array['name'][$y + $x];
                $data['start'] = $activity_array['start'][$y + $x];
                $data['end'] = $activity_array['end'][$y + $x];
                $data['budget'] = $activity_array['budget'][$y + $x];
                $data['project_id'] = $project->id;

                $activity->create($data);
            }
        }
            // -->

        return redirect()->route('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     */
    public function destroy($id)
    {
        //
    }
}
