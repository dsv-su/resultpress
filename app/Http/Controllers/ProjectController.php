<?php

namespace App\Http\Controllers;

use App\Activity;
use App\ActivityUpdate;
use App\Area;
use App\Events\PartnerUpdate;
use App\Events\PartnerUpdateEvent;
use App\File;
use App\Invite;
use App\Organisation;
use App\Outcome;
use App\OutcomeUpdate;
use App\Output;
use App\OutputUpdate;
use App\Project;
use App\ProjectArea;
use App\ProjectHistory;
use App\ProjectOwner;
use App\ProjectPartner;
use App\ProjectReminder;
use App\ProjectUpdate;
use App\Services\ACLHandler;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Swaggest\JsonDiff\Exception;
use Swaggest\JsonDiff\JsonDiff;

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
        //If user should be redirected to profile setting page
        if ($user->setting == true) {
            return redirect()->intended('/home');
        }
        return redirect()->intended('/');
    }

    /**
     * Display a listing of the resource.
     *
     */

    public function home()
    {
        /****
         *   This is the first page the user is routed to. It shows the three sections if the user is a spider or administrator.
         *  If the user is a Partner it shows the projects the user is assigned (invited) to
         ****/
        return redirect()->route('search');
        $data['program_areas'] = Area::all();

        if ($user = Auth::user()) {
            if ($user->hasRole(['Administrator', 'Program administrator', 'Spider'])) {

                $data['user'] = User::find(auth()->user()->id);
                $data['areas'] = Area::with('project_area.project.project_owner.user')->get();
                $data['otherprojects'] = Project::doesntHave('project_area')->get();

                return view('home.index', $data);

            } elseif ($user->hasRole(['Partner'])) {

                $id = ProjectPartner::where('partner_id', $user->id)->pluck('project_id');
                $projects = Project::with('project_owner.user', 'project_area.area')->whereIn('id', $id)->latest()->get();

                return view('home.partner', ['projects' => $projects, 'user' => $user], $data);
            }

        } elseif (Auth::check()) abort(403);

        return redirect()->route('partner-login');
    }

    public function index()
    {
        $program_areas = Area::all();
        if ($user = Auth::user()) {
            if ($user->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
                $projects = Project::with('project_owner.user', 'project_area.area')->latest()->get();
                return view('project.index', ['projects' => $projects, 'user' => $user, 'program_areas' => $program_areas]);
            } elseif ($user->hasRole(['Partner'])) {
                $id = ProjectPartner::where('partner_id', $user->id)->pluck('project_id');
                $projects = Project::with('project_owner.user', 'project_area.area')->whereIn('id', $id)->latest()->get();
                return view('project.index', ['projects' => $projects, 'user' => $user, 'program_areas' => $program_areas]);
            }
        } elseif (Auth::check()) abort(403);
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
        $activities = $project->activities;
        $outputs = $project->submitted_outputs();
        $moneyspent = 0;
        $budget = 0;
        foreach ($activities as $a) {
            $activityupdates = ActivityUpdate::where('activity_id', $a->id)
                ->join('project_updates', 'project_update_id', '=', 'project_updates.id')
                ->where('project_updates.status', 'approved')
                ->orderBy('start', 'asc')
                ->get(['activity_updates.*']);

            $comments = array();
            // For cumulative displaying just the recent update comment.
            if (!$project->cumulative) {
                foreach ($activityupdates as $au) {
                    $puindex = 0;
                    foreach ($project_updates as $index => $pu) {
                        if ($pu->id == $au->project_update_id) {
                            $puindex = $index + 1;
                        }
                    }
                    $comments[$puindex] = ['comments' => $au->comment, 'pu' => $au->project_update_id];
                }
            } elseif (!$activityupdates->isEmpty()) {
                $comments[] = $activityupdates->last()->comment;
            }

            foreach ($activityupdates as $au) {
                $moneyspent += $au->money;
                $a->moneyspent += $au->money;
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
            if ($o->status == 'custom') {
                $o->valuestatus = 0;
            } elseif ($valuesum >= $o->target) {
                $o->valuestatus = 3;
            } elseif ($valuesum == 0) {
                $o->valuestatus = 2;
            } else {
                $o->valuestatus = 1;
            }
        }

        foreach ($project->outcomes as $outcome) {
            if (!$outcome->outputs) {
                $outcome->outputs = json_encode(array());
            }
        }

        $project->dates = $this->project_dates($project);
        $project->projectstart = !$activities->isEmpty() ? Activity::where('project_id', $project->id)->orderBy('start', 'asc')->first()->start->format('d/m/Y') : null;
        $project->projectend = !$activities->isEmpty() ? Activity::where('project_id', $project->id)->orderBy('end', 'desc')->first()->end->format('d/m/Y') : null;
        $project->updatesnumber = count($project_updates);
        $project->recentupdate = !$project_updates->isEmpty() ? $project_updates->sortBy('created_at')->values()->last()->created_at->format('d/m/Y') : null;
        $project->moneyspent = $moneyspent;
        $project->budget = $budget;

        $projectDeadlines = ProjectReminder::where('project_id', $project->id)->get();

        return view('project.show', ['project' => $project, 'activities' => $activities, 'outputs' => $outputs, 'deadlines' => $projectDeadlines]);
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
        //Session
        $links = session()->has('links') ? session('links') : [];
        $currentLink = request()->path(); // Getting current URI like 'category/books/'
        array_unshift($links, $currentLink); // Putting it in the beginning of links array
        session(['links' => $links]); // Saving links array to the session

        if ($user = Auth::user()) {
            if ($user->hasPermissionTo('project-create') || ($project->id && $user->hasPermissionTo('project-' . $project->id . '-edit'))) {
                return view('project.form', [
                    'project' => $project,
                    'activities' => $project->activities,
                    'outputs' => $project->submitted_outputs(),
                    'project_areas' => $project->project_area,
                    'areas' => Area::all(),
                    'old_pa' => $project->project_area->pluck('area_id')->toArray(),
                    'users' => User::all(),
                    'old_users' => ProjectOwner::where('project_id', $project->id)->pluck('user_id')->toArray(),
                    'partners' => ProjectPartner::where('project_id', $project->id)->pluck('partner_id')->toArray(),
                    'project_reminders' => ProjectReminder::where('project_id', $project->id)->get(),
                    'invites' => Invite::where('project_id', $project->id)->get(),
                    'organisations' => Organisation::all()
                    /*'managers' => User::whereHas('project_owner', function ($query) use($project) {
                                    return $query->where('project_id', $project->id);
                                    })->get()*/
                ]);
            } else {
                abort(403);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Project $project
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Project $project)
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-edit')) {
                abort(403);
            }
        }

        request()->validate([
            'project_name' => 'required',
            'user_id' => 'required',
        ]);
        $project->name = request('project_name');
        $project->description = request('project_description');
        $project->start = Carbon::createFromFormat('d-m-Y', request('project_start') ?? null)->format('Y-m-d');
        $project->end = Carbon::createFromFormat('d-m-Y', request('project_end') ?? null)->format('Y-m-d');
        $project->currency = request('project_currency') ?? null;
        $project->cumulative = request('project_cumulative');
        $id = $project->save();

        //Update Program Area
        foreach (ProjectArea::where('project_id', $project->id)->get() as $old_pa) {
            ProjectArea::find($old_pa->id)->delete();
        }
        if (request('project_area')) {
            foreach (request('project_area') as $project_area) {
                $new_pa = new ProjectArea();
                if ($project->id) {
                    $new_pa->project_id = $project->id;
                } else {
                    $new_pa->project_id = $id;
                }
                $new_pa->area_id = $project_area;
                $new_pa->save();
            }
        }

        //Set project reminders
        if (count($request->project_reminder ?? []) > 0) {
            foreach ($request->project_reminder as $key => $reminder) {
                ProjectReminder::where('project_id', $project->id)->delete();
            }
            foreach ($request->project_reminder as $key => $reminder) {
                ProjectReminder::updateOrCreate([
                    'project_id' => $project->id,
                    'set' => Carbon::createFromFormat('d-m-Y', $request->project_reminder_date[$key])->format('Y-m-d')
                ],
                    [
                        'name' => $request->project_reminder_name[$key],
                        'reminder' => $request->project_reminder[$key],
                        'reminder_due_days' => $request->project_reminder_due_days[$key]
                    ]);
            }
        }


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
        $activity_array['priority'] = request('activity_priority');
        //Email reminder
        //$activity_array['reminder'] = request('activity_reminder');
        //$activity_array['reminder_due_days'] = request('activity_reminder_due_days');
        //Outputs
        $output_array['id'] = request('output_id') ?? null;
        $output_array['indicator'] = request('output_indicator');
        $output_array['target'] = request('output_target');
        //Outcomes
        $outcome_array['id'] = request('outcome_id') ?? null;
        $outcome_array['name'] = request('outcome_name');

        //Remove deleted activities
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
                //$data['reminder'] = $activity_array['reminder'][$key];
                //$data['reminder_due_days'] = $activity_array['reminder_due_days'][$key];
                $data['project_id'] = $project->id;
                $data['priority'] = $activity_array['priority'][$key];
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

        // Remove deleted outcomes
        foreach ($project->outcomes as $o) {
            if (!$outcome_array['id'] || !in_array($o->id, $outcome_array['id'])) {
                Outcome::findOrFail($o->id)->delete();
            }
        }

        if (!empty($outcome_array['id'])) {
            foreach ($outcome_array['id'] as $key => $id) {
                $data = array();
                $data['name'] = $outcome_array['name'][$key];
                $data['project_id'] = $project->id;
                $data['user_id'] = Auth::user()->id;
                if ($id) {
                    Outcome::where('id', $id)->update($data);
                    //Log outcome update
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn(Outcome::find($id))
                        ->log('OutcomeUpdate');
                } else {
                    $newoutcome = Outcome::create($data);
                    //Log new outcome
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn(Outcome::find($newoutcome->id))
                        ->log('NewOutcome');
                }
            }
        }

        // Update Project managers and partners
        $project_owners = ProjectOwner::where('project_id', $project->id)->get();
        $project_partners = ProjectPartner::where('project_id', $project->id)->get();
        //Erase existing owners
        foreach ($project_owners as $project_owner) {
            $owner = ProjectOwner::find($project_owner->id);
            $user = User::find($owner->user_id);
            $user->revokePermissionTo('project-' . $project->id . '-list');
            $user->revokePermissionTo('project-' . $project->id . '-edit');
            $user->revokePermissionTo('project-' . $project->id . '-update');
            $user->revokePermissionTo('project-' . $project->id . '-delete');
            $owner->delete();
        }

        //Store new managers
        if ($request->user_id) {
            foreach ($request->user_id as $owner) {
                $new_owner = new ProjectOwner();
                $new_owner->project_id = $project->id;
                $new_owner->user_id = $owner;
                $new_owner->save();
                //Give specific project permissions to user
                $user = User::find($owner);
                $user->givePermissionTo('project-' . $project->id . '-list', 'project-' . $project->id . '-edit', 'project-' . $project->id . '-update', 'project-' . $project->id . '-delete');
            }
        }
        //Erase existing partners
        if ($project_partners) {
            foreach ($project_partners as $project_partner) {
                $partner = ProjectPartner::find($project_partner->id);
                $user = User::find($partner->partner_id);
                $user->revokePermissionTo('project-' . $project->id . '-list');
                $user->revokePermissionTo('project-' . $project->id . '-update');
                $partner->delete();
            }
        }
        //Store new partners
        if ($request->partner_id) {
            foreach ($request->partner_id as $partner) {
                $new_partner = new ProjectPartner();
                $new_partner->project_id = $project->id;
                $new_partner->partner_id = $partner;
                $new_partner->save();
                //Give specific project permissions to partner
                $user = User::find($partner);
                $user->givePermissionTo('project-' . $project->id . '-list', 'project-' . $project->id . '-update');
            }
        }
        $project->refresh();
        // Save to history
        $history = new ProjectHistory();
        $history->project_id = $project->id;
        $history->user_id = Auth::user()->id;
        $history->data = $project->wrapJson();
        if ($history->data) {
            $history->save();
        }

        return redirect()->route('project_show', $project);
    }

    public function write_update(Project $project)
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-update')) {
                abort(403);
            }
        }
        return view('project.update', ['project' => $project]);
    }

    public function save_update(Project $project, Request $request)
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-update')) {
                abort(403);
            }
        }
        $projectupdate = ProjectUpdate::firstOrNew(['id' => request('project_update_id') ?? 0]);
        $projectupdate->project_id = $project->id;
        $projectupdate->summary = request('project_update_summary') ?? null;
        $projectupdate->state = request('project_state') ?? null;
        $dates = explode(' - ', request('dates'));
        $projectupdate->start = Carbon::createFromFormat('d/m/Y', $dates[0]);
        $projectupdate->end = Carbon::createFromFormat('d/m/Y', $dates[1]);

        $status = '';
        if ($request->input('draft')) {
            $status = 'draft';
        } else if ($request->input('submit')) {
            if (Auth::user()->hasRole(['Spider', 'Administrator'])) {
                $status = 'approved';
            } else {
                $status = 'submitted';
            }
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
        $activity_update_array['comment'] = request('activity_comment') ?? null;
        $activity_update_array['money'] = request('activity_money');
        $activity_update_array['state'] = request('activity_state') ?? null;

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
                $activityupdate->money = $activity_update_array['money'][$key];
                $activityupdate->state = $activity_update_array['state'][$key];
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

        // Outcome updates
        $outcome_update_array['outcome_id'] = request('outcome_id');
        $outcome_update_array['outcome_update_id'] = request('outcome_update_id');
        $outcome_update_array['outcome_outputs'] = request('outcome_outputs');
        $outcome_update_array['outcome_summary'] = request('outcome_summary');

        // Remove deleted activity updates
        foreach ($projectupdate->outcome_updates()->get() as $ou) {
            if (!$outcome_update_array['outcome_id'] || !in_array($ou->id, $outcome_update_array['outcome_update_id'])) {
                OutcomeUpdate::findOrFail($ou->id)->delete();
            }
        }

        if ($outcome_update_array['outcome_id']) {
            foreach ($outcome_update_array['outcome_id'] as $key => $id) {
                $ou = OutcomeUpdate::firstOrNew(['id' => $outcome_update_array['outcome_update_id'][$key]]);
                $ou->outcome_id = Outcome::findOrFail($id)->id;
                $ou->outputs = $outcome_update_array['outcome_outputs'][$key];
                $ou->summary = $outcome_update_array['outcome_summary'][$key];
                $ou->completed_on = Carbon::now();
                $ou->project_update_id = $projectupdate_id;
                $ou->save();
                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($ou)
                    ->log('OutcomeUpdateReported');
                if ($status == 'approved') {
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($ou)
                        ->log('OutcomeUpdateApproved');
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
                    $data['project_id'] = $project->id;
                    if ($status == 'approved') {
                        $data['status'] = 'custom';
                    }
                    $id = Output::create($data)->id;
                }
                $outputupdate = OutputUpdate::firstOrNew(['id' => $output_update_array['output_update_id'][$key]]);
                $output = Output::findOrFail($id);
                $outputupdate->output_id = $output->id;
                $outputupdate->value = $output_update_array['value'][$key];
                $outputupdate->project_update_id = $projectupdate_id;
                // Log update for submitted OutputUpdate --> Added outputs should be i draft (TODO)
                if ($status == 'submitted') {
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
        // Fire an event
        event(new PartnerUpdateEvent($projectupdate));

        // Save to history
        if ($status == 'submitted' || $status == 'approved') {
            $history = new ProjectHistory();
            $history->project_id = $project->id;
            $history->user_id = Auth::user()->id;
            $history->data = $project->wrapJson();
            if ($history->data) {
                $history->save();
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
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-delete')) {
                abort(403);
            }
        }
        //Find associated owners
        $owners = ProjectOwner::where('project_id', $project->id)->pluck('user_id');
        //Revoke owners permissions
        foreach ($owners as $owner) {
            $user = User::find($owner);
            $user->revokePermissionTo('project-' . $project->id . '-list');
            $user->revokePermissionTo('project-' . $project->id . '-edit');
            $user->revokePermissionTo('project-' . $project->id . '-update');
            $user->revokePermissionTo('project-' . $project->id . '-delete');
            $project_owner = ProjectOwner::where('user_id', $owner);
            $project_owner->delete();
        }

        //Find associated partners
        $partners = ProjectPartner::where('project_id', $project->id)->pluck('partner_id');
        //Revoke partners permissions
        foreach ($partners as $partner) {
            $user = User::find($partner);
            $user->revokePermissionTo('project-' . $project->id . '-list');
            $user->revokePermissionTo('project-' . $project->id . '-update');
            $project_partner = ProjectPartner::where('partner_id', $partner);
            $project_partner->delete();
        }

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

        //Delete project belongs to project area
        ProjectArea::where('project_id', $project->id)->delete();

        // Delete project
        $project->delete();
        return redirect()->route('project_home');
    }

    public function archive(Project $project)
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-edit')) {
                abort(403);
            }
        }
        $project->archived = true;
        $project->update();
        return redirect()->route('project_show', $project);
    }

    public function unarchive(Project $project)
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-edit')) {
                abort(403);
            }
        }
        $project->archived = false;
        $project->update();
        return redirect()->route('project_show', $project);
    }

    public function completeActivity(Request $request)
    {
        $activity = Activity::find($request->activity_id);
        $activity->completed = $request->activity_completed;
        $activity->update();
        return Response()->json([
            'message' => 'Success',
            'text' => 'Marked as completed'
        ]);
    }

    /**
     * @throws Exception
     */
    public function history(Project $project)
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-edit')) {
                abort(403);
            }
        }
        $data = array();
        foreach ($project->histories as $index => $history) {
            $data[$index]['created'] = $history->created_at->format('d/m/Y');
            $data[$index]['user'] = $history->user->name;
            if ($index) {
                $previous = json_decode($project->histories()->orderBy('id', 'desc')->where('id', '<', $history->id)->first()->data);
                $current = json_decode($history->data);
                $diff = new JsonDiff($previous, $current, JsonDiff::COLLECT_MODIFIED_DIFF);
                if ($diff->getDiffCnt()) {
                    if (!empty($diff->getModifiedDiff())) {
                        foreach ($diff->getModifiedNew() as $key => $m) {
                            if ($key == 'project_owner') {
                                foreach ($m as $i => $item) {
                                    $item->old_name = $previous->$key[$i]->name;
                                    $item->old_user_id = $previous->$key[$i]->user_id;
                                    $data[$index]['modified'][$key][] = $item;
                                }
                            } elseif ($key == 'partners') {
                                foreach ($m as $i => $item) {
                                    $item->old_name = $previous->$key[$i]->name;
                                    $item->old_partner_id = $previous->$key[$i]->partner_id;
                                    $data[$index]['modified'][$key][] = $item;
                                }
                            } elseif ($key == 'areas') {
                                foreach ($m as $i => $item) {
                                    $item->old_name = $previous->$key[$i]->name;
                                    $item->old_description = $previous->$key[$i]->description;
                                    $data[$index]['modified'][$key][] = $item;
                                }
                            } elseif (is_array($m) || is_object($m)) {
                                foreach ($m as $i => $item) {
                                    $data[$index]['modified'][$key][$diff->getRearranged()->$key[$i]->id] = $item;
                                }
                            } elseif ($key == 'project_updates') {
                                foreach ($m as $i => $pu) {
                                    $data[$index]['modified'][$key][$diff->getRearranged()->$key[$i]->id] = $pu;
                                }
                            } else {
                                $data[$index]['modified']['project'][$key] = ($key == 'start' || $key == 'end') ? Carbon::parse($m)->format('d/m/Y') : $m;
                            }
                        }
                    }
                    if (!empty($diff->getAdded())) {
                        foreach ($diff->getAdded() as $key => $a) {
                            if (!is_array($a)) {
                                $a = array($a);
                            }
                            foreach ($a as $id => $value) {
                                if ($key == 'project_updates') {
                                    $data[$index]['added'][$key][$diff->getRearranged()->$key[$id]->id] = $value;
                                } else {
                                    $data[$index]['added'][$key][] = $value;
                                }
                            }
                        }
                    }
                    if (!empty($diff->getRemoved())) {
                        foreach ($diff->getRemoved() as $key => $r) {
                            if (is_object($r) || is_array($r)) {
                                foreach ($r as $value) {
                                    $data[$index]['removed'][$key][] = $value;
                                }
                            } else {
                                $data[$index]['removed'][$key] = $r;
                            }
                        }
                    }
                } else {
                    $data[$index]['modified'] = 'No changes';
                }
            } else {
                $data[$index]['modified'] = 'Initial version';
            }
        }

        return view('project.history', ['project' => $project, 'history' => $data]);
    }
}
