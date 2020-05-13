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
        return view('project.show', ['project' => $project, 'activities' => Activity::where('project_id', $project->id)->get()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Project  $project
     *
     */
    public function edit(Project $project)
    {
        return view('project.form', ['project' => $project, 'activities' => Activity::where('project_id', $project->id)->get()]);
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
        request()->validate([
            'project_name' => 'required',
            'project_description' => 'required'
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
        $activity_array['start'] = request('activity_start');
        $activity_array['end'] = request('activity_end');
        $activity_array['name'] = request('activity_name');
        $activity_array['budget'] = request('activity_budget');
        //

        // Remove deleted activities
        foreach (Activity::where('project_id', $project->id)->get() as $a) {
            if (!$activity_array['id'] || !in_array($a->id, $activity_array['id'])) {
                Activity::findOrFail($a->id)->delete();
            }
        }

        if (!empty($activity_array['id'])) {
            foreach ($activity_array['id'] as $key => $id) {
                $data['title'] = $activity_array['name'][$key];
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
