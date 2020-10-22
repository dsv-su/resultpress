<?php

namespace App\Http\Controllers;

use App\Activity;
use App\ActivityUpdate;
use App\File;
use App\Output;
use App\OutputUpdate;
use App\Project;
use App\ProjectOwner;
use App\ProjectPartner;
use App\ProjectUpdate;
use App\Services\ACLHandler;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProjectController extends Controller
{
    public function shibboleth()
    {
        //Users entering via the Shibboleth login
        $user = Auth::user();
        //All spider-users get the role 'Spider'
        $user->assignRole('Spider');
        //All spider-users can create projects
        $user->givePermissionTo('project-list');
        $user->givePermissionTo('project-create');
        //Give these users role Admin
        if ($user->name == 'Ryan Dias') $user->assignRole('Administrator');
        if ($user->name == 'Pavel Sokolov') $user->assignRole('Administrator');
        if ($user->name == 'Erik Thuning') $user->assignRole('Administrator');
        return redirect()->action('ProjectController@index');
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        if ($user = Auth::user()) {
            if ($user->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
                $projects = Project::with('project_owner.user')->latest()->get();
                return view('project.index', ['projects' => $projects, 'user' => $user]);
            } elseif ($user->hasRole(['Partner'])) {
                $id = ProjectPartner::where('partner_id', $user->id)->pluck('project_id');
                $projects = Project::with('project_owner.user')->whereIn('id', $id)->latest()->get();
                return view('project.index', ['projects' => $projects, 'user' => $user]);
            }
        }

        elseif (Auth::check()) return abort(403);
        else return redirect()->route('partner-login');

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
        $project_updates = ProjectUpdate::where('project_id', $project->id)->where('project_updates.status', 'approved')->get();
        $activities = $project->activities()->get();
        $outputs = $project->submitted_outputs();
        $moneyspent = 0;
        $budget = 0;
        foreach ($activities as $a) {
            $activityupdates = ActivityUpdate::where('activity_id', $a->id)
                ->join('project_updates', 'project_update_id', '=', 'project_updates.id')
                ->where('project_updates.status', 'approved')
                ->orderBy('date', 'asc')
                ->get(['activity_updates.*']);

            $comments = array();
            // For cumulative displaying just the recent update comment.
            if (!$project->cumulative) {
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
            } elseif (!$activityupdates->isEmpty()) {
                $comments[] = $activityupdates->last()->comment;
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
                ->where('project_updates.status', 'approved')
                ->get(['output_updates.*']);
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

    public function project_dates(Project $project)
    {
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
            'activities' => $project->activities()->get(),
            'outputs' => $project->submitted_outputs()
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
        ]);
        $project->name = request('project_name');
        $project->description = request('project_description');
        $project->start = Carbon::createFromFormat('d-m-Y', request('project_start') ?? null)->format('Y-m-d');
        $project->status = 0; // temp value
        $project->end = Carbon::createFromFormat('d-m-Y', request('project_end') ?? null)->format('Y-m-d');
        $project->currency = request('project_currency') ?? null;
        $project->cumulative = request('project_cumulative');
        $project->save();

        //Create permissions for a new project
        if (request('new_project') == 1) {
            //Adds the logged in user as project owner
            $owner = new ProjectOwner();
            $owner->project_id = $project->id;
            $owner->user_id = Auth::id() ?? 1;
            $owner->save();

            //Logged in user can Read, Edit and Delete project
            $user = Auth::user();
            $acl = new ACLHandler($project, $user);
            $acl->setNewProjectPermissions();

        }
        //Activities
        //Request from form --> this should later be refactored
        $activity_array['id'] = request('activity_id') ?? null;
        $activity_array['name'] = request('activity_name');
        $activity_array['description'] = request('activity_description') ?? null;
        $activity_array['template'] = request('activity_template') ?? null;
        $activity_array['start'] = request('activity_start');
        $activity_array['end'] = request('activity_end');
        $activity_array['name'] = request('activity_name');
        $activity_array['budget'] = request('activity_budget');
        //Email reminder
        $activity_array['reminder'] = request('activity_reminder');
        $activity_array['reminder_due_days'] = request('activity_reminder_due_days');
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
                $data['template'] = $activity_array['template'][$key];
                //Transform dates from datepicker into the right format before saving to database
                $data['start'] = Carbon::createFromFormat('d-m-Y', $activity_array['start'][$key])->format('Y-m-d');
                $data['end'] = Carbon::createFromFormat('d-m-Y', $activity_array['end'][$key])->format('Y-m-d');
                $data['budget'] = $activity_array['budget'][$key];
                $data['reminder'] = $activity_array['reminder'][$key];
                $data['reminder_due_days'] = $activity_array['reminder_due_days'][$key];
                $data['project_id'] = $project->id;
                if ($id) {
                    Activity::where('id', $id)->update($data);
                    //Log activity update
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn(Activity::find($id))
                        ->log('ActivityUpdate');
                } else {
                    $newactivity = Activity::create($data);
                    //Log new activity
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn(Activity::find($newactivity->id))
                        ->log('NewActivity');
                }
            }
        }

        // Remove deleted outputs
        foreach ($project->submitted_outputs() as $o) {
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
                    //Log output update
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn(Output::find($id))
                        ->log('OutputUpdate');
                } else {
                    $data['status'] = 'default';
                    $newoutput = Output::create($data);
                    //Log new output
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn(Output::find($newoutput->id))
                        ->log('NewOutput');
                }
            }
        }

        // -->

        return redirect()->route('project_show', $project);
    }

    public function write_update(Project $project)
    {
        if ($project->hasDraft() && $project->cumulative) {
            return abort(401);
        }
        return view('project.update', ['project' => $project]);
    }

    public function save_update(Project $project, Request $request)
    {
        $projectupdate = ProjectUpdate::firstOrNew(['id' => request('project_update_id') ?? 0]);
        $projectupdate->project_id = $project->id;
        $projectupdate->summary = request('project_update_summary') ?? null;

        $status = '';
        if ($request->input('draft')) {
            $status = 'draft';
        } else if ($request->input('submit')) {
            $status = 'submitted';
        } else if ($request->input('delete')) {
            return redirect()->route('projectupdate_delete', $projectupdate);
        }

        $projectupdate->status = $status;

        //Adds the logged in user as projectupdate author
        $projectupdate->user_id = Auth::id() ?? 1;

        $projectupdate->save();
        //Log update for submitted update
        if ($status == 'submitted') {
            activity()
                ->causedBy($projectupdate->user_id)
                ->performedOn($projectupdate)
                ->log('ProjectUpdate');
        }

        $projectupdate_id = $projectupdate->id;

        // Process activity updates
        $activity_update_array['activity_id'] = request('activity_id');
        $activity_update_array['activity_update_id'] = request('activity_update_id');
        $activity_update_array['comment'] = request('activity_comment');
        $activity_update_array['status'] = request('activity_status');
        $activity_update_array['money'] = request('activity_money');
        $activity_update_array['date'] = request('activity_date') ?? null;

        // Remove deleted activity updates
        foreach ($projectupdate->activity_updates()->get() as $au) {
            if (!$activity_update_array['activity_update_id'] || !in_array($au->id, $activity_update_array['activity_update_id'])) {
                ActivityUpdate::findOrFail($au->id)->delete();
            }
        }

        if ($activity_update_array['activity_id']) {
            foreach ($activity_update_array['activity_id'] as $key => $id) {
                $activityupdate = ActivityUpdate::firstOrNew(['id' => $activity_update_array['activity_update_id'][$key]]);
                $activityupdate->activity_id = Activity::findOrFail($id)->id;
                $activityupdate->comment = $activity_update_array['comment'][$key];
                $activityupdate->status = $activity_update_array['status'][$key];
                $activityupdate->money = $activity_update_array['money'][$key];
                $activityupdate->date = $activity_update_array['date'][$key];
                $activityupdate->project_update_id = $projectupdate_id;
                $activityupdate->save();
                //Log update for submitted ActivityUpdate
                if ($status == 'submitted') {
                    activity()
                        ->causedBy($projectupdate->user_id)
                        ->performedOn($activityupdate)
                        ->log('ActivityUpdate');
                }
            }
        }

        // Process output updates
        $output_update_array['output_id'] = request('output_id');
        $output_update_array['output_update_id'] = request('output_update_id');
        $output_update_array['value'] = request('output_value');

        // Remove deleted output updates
        foreach ($projectupdate->output_updates()->get() as $ou) {
            if (!$output_update_array['output_update_id'] || !in_array($ou->id, $output_update_array['output_update_id'])) {
                OutputUpdate::findOrFail($ou->id)->delete();
            }
        }

        if ($output_update_array['output_id']) {
            foreach ($output_update_array['output_id'] as $key => $id) {
                if (!is_numeric($output_update_array['output_id'][$key])) {
                    // Create new output since it's an unexpected one
                    $data = array();
                    $data['indicator'] = $output_update_array['output_id'][$key];
                    $data['target'] = 0;
                    $data['status'] = ($status == 'draft') ? 'draft' : null;
                    $data['project_id'] = $project->id;
                    $id = Output::create($data)->id;
                }
                $outputupdate = OutputUpdate::firstOrNew(['id' => $output_update_array['output_update_id'][$key]]);
                $output = Output::findOrFail($id);
                $outputupdate->output_id = $output->id;
                $outputupdate->value = $output_update_array['value'][$key];
                $outputupdate->project_update_id = $projectupdate_id;
                // Log update for submitted OutputUpdate --> Added outputs should be i draft (TODO)
                if ($status == 'submitted') {
                    if ($output->status == 'draft') {
                        $output->status = 'custom';
                        $output->save();
                    }
                    activity()
                        ->causedBy($projectupdate->user_id)
                        ->performedOn($outputupdate)
                        ->log('OutputUpdate');
                }
                $outputupdate->save();
            }
        }

        // Update file reference
        $file_ids = request('file_id') ?? null;
        if ($file_ids) {
            if (!is_array($file_ids)) {
                $file_ids = array($file_ids);
            }
            // Remove deleted attachments
            foreach ($projectupdate->files()->get() as $file) {
                if (!in_array($file->id, $file_ids)) {
                    File::findOrFail($file->id)->delete();
                }
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
            $pu->delete();
        }

        // Delete outputs and activities
        Activity::where('project_id', $project->id)->forcedelete();
        Output::where('project_id', $project->id)->forcedelete();

        // Delete project owners
        ProjectOwner::where('project_id', $project->id)->delete();
        // Delete project partners
        ProjectPartner::where('project_id', $project->id)->delete();

        // Delete project
        $project->delete();
        return redirect()->route('project_home');
    }
}
