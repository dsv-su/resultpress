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
use App\Http\Requests\UpdateProjectRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Swaggest\JsonDiff\Exception;
use Swaggest\JsonDiff\JsonDiff;
use App\Notifications\ProjectAccepted;
use App\Notifications\ProjectRejected;
use App\Notifications\ProjectChangeAccepted;
use App\Notifications\ProjectChangeRejected;
use App\Notifications\ProjectChangeRequest;
use App\Notifications\NewProjectRequest;
use App\Notifications\ProjectUpdated;

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
                $comments[] = ['comments' => $activityupdates->last()->comment];
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

        $aggregated_outpus = $project->aggregated_outputs();
        foreach ($aggregated_outpus ?? [] as $ao) {
            $os = json_decode($ao->target);
            $valuesum = 0;
            $target = 0;
            foreach ($os ?? [] as $o) {
                $o = $outputs->first(function($item) use ($o) {
                    return $item->id == $o;
                });
                $valuesum += $o->valuesum;
                $target += $o->target;
            }
            $ao->target = $target;
            $ao->valuesum = $valuesum;
            if ($valuesum >= $target) {
                $ao->valuestatus = 3;
            } elseif ($valuesum == 0) {
                $ao->valuestatus = 2;
            } else {
                $ao->valuestatus = 1;
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

        return view('project.show', ['project' => $project, 'activities' => $activities, 'outputs' => $outputs, 'deadlines' => $projectDeadlines, 'aggregated_outputs' => $aggregated_outpus]);
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
        //Storing path in Session
        $links = session()->has('links') ? session('links') : [];
        $currentLink = request()->path(); // Getting current URI like 'project/id/edit'
        array_unshift($links, $currentLink); // Putting it in the beginning of links array
        session(['links' => $links]); // Saving links array to the session
        $formView = 'project.form';

        if($project->object_type === 'project_change_request') {
            $formView = 'project.change_request_form';
        }

        if ($user = Auth::user()) {
            if ($user->hasPermissionTo('project-create') || ($project->id && $user->hasPermissionTo('project-' . $project->id . '-edit'))) {

                if(!$project->id){
                    $impactReminder = new ProjectReminder(
                        [
                            'type' => 'impact',
                            'name' => 'This is a reminder to report on the impact of your project or initiative, please follow the link below to view the project',
                            'set' => $project->end ? $project->end->addMonths(24) : Carbon::now()->addMonths(24),
                            'reminder' => 1,
                        ]
                    );
                } else {
                    $impactReminder = ProjectReminder::firstOrCreate(
                        ['project_id' => $project->id, 'type' => 'impact'],
                        [
                            'name' => 'This is a reminder to report on the impact of your project or initiative, please follow the link below to view the project',
                            'set' => $project->end ? $project->end->addMonths(24) : Carbon::now()->addMonths(24),
                            'reminder' => 1,
                        ]
                    );
                }

                return view($formView, [
                    'project' => $project,
                    'activities' => $project->activities,
                    'outputs' => $project->submitted_outputs(),
                    'aggregated_outputs' => $project->aggregated_outputs(),
                    'project_areas' => $project->areas()->get(),
                    'areas' => Area::all(),
                    'old_pa' => $project->areas()->get()->pluck('id')->toArray(),
                    'users' => User::whereDoesntHave('roles', function ($query) {
                        return $query->where('name', 'partner');
                    })->orderBy('name', 'asc')->get(),
                    'partnerusers' => User::whereHas('roles', function ($query) {
                        return $query->where('name', 'partner');
                    })->orderBy('name', 'asc')->get(),
                    'old_users' => ProjectOwner::where('project_id', $project->id)->pluck('user_id')->toArray(),
                    'partners' => ProjectPartner::where('project_id', $project->id)->pluck('partner_id')->toArray(),
                    'project_reminders' => $project->reminders()->get(),
                    'impact_reminder' => $impactReminder ?? null,
                    'invites' => Invite::where('project_id', $project->id)->get(),
                    'organisations' => Organisation::all(),
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
     * @param UpdateProjectRequest $request
     * @param Project $project
     *
     * @return RedirectResponse
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        // if request not valid, redirect back to form with errors
        if (!$request->validated()) {
            return redirect()->back()->withErrors($request->errors());
        }

        $notifications = [
            'new_project' => false,
            'change_request' => false,
            'project_updated' => false,
        ];

        // Add or Update project.
        if ($request->has('new_project')) {
            $current_user = Auth::id();
            $project = new Project(['name' => $request->name]);
            if (Auth::user()->hasRole('Partner')) {
                $project->object_type = 'project_add_request';
                $notifications['new_project'] = true;
                $current_user = 1;
            }
            $project->save();
            $project->project_owner()->create(['user_id' => $current_user ?? 1]);
            $acl = new ACLHandler($project, User::find($current_user));
            $acl->setNewProjectPermissions();
        }

        if (Auth::user()->hasRole('Partner') && $project->object_type == 'project') {
            $original_project = $project;
            $project = $project->replicate();
            $project->object_id = $original_project->id;
            $project->object_type = 'project_change_request';
            $project->save();
            // $project->project_owner()->create(['user_id' => Auth::id() ?? 1]);
            $acl = new ACLHandler($project, Auth::user());
            $acl->setNewProjectPermissions();
            // TODO: we still need to set permissions here, but we need to do it in a way that doesn't duplicate the permissions
            $notifications['change_request'] = true;
        }

        if (Auth::user()->hasRole('Partner') && $project->object_type == 'project_change_request') {
            $notifications['change_request'] = true;
        }

        if (in_array($project->object_type, ['project_add_request'])) {
            $notifications['project_updated'] = true;
        }


        $project->update($request->all());

        $project->project_area()->sync($request->input('project_area', []));

        // Managing project reminders
        $reminders = $request->collect('reminders'); // Get reminders from request
        $reminders_to_remove = $project->reminders->pluck('id')->diff($reminders->pluck('id')); // Get reminders to remove
        $reminders->each(
            function ($reminder) use ($project) {
                $project->reminders()->updateOrCreate( ['id' => $reminder['id'] ?? null], $reminder);
            }
        );
        $project->reminders()->whereIn('id', $reminders_to_remove)->delete(); // Delete reminders to remove

        // Manage activities
        $activities = $request->collect('activities'); // Get all activities from the request and remove duplicates and null values.
        $activities_to_remove = $project->activities->pluck('id')->diff($activities->pluck('id')); // Get all activities that are not in the request and remove them from the project.
        $activities->each(
            function ($activity) use ($project) {
                $project->activities()->updateOrCreate(['id' => $activity['id'] ?? null], $activity);
                }
        );
        $project->activities()->whereIn('id', $activities_to_remove)->delete(); // Delete activities.

        // Manage outputs
        $outputs = $request->collect('outputs'); // Get all outputs from the request.
        $outputs_to_remove = $project->outputs->pluck('id')->diff($outputs->pluck('id')); // Get all outputs that are not in the request.
        $outputs->each(
            function ($output) use ($project) {
                $output['target'] = $output['status'] == 'aggregated' ? json_encode([$output['target']]): $output['target'];
                $project->outputs()->updateOrCreate(['id' => $output['id'] ?? null], $output);
        }
        );
        $project->outputs()->whereIn('id', $outputs_to_remove)->delete(); // Delete outputs.


        // Manage outcomes
        $outcomes = $request->collect('outcomes'); // Get all outcomes from the request.
        $outcomes_to_remove = $project->outcomes->pluck('id')->diff($outcomes->pluck('id')); // Get all outcomes that are not in the request.
        $outcomes->each(
            function ($outcome) use ($project) {
                $outcome['user_id'] = Auth::id();
                $project->outcomes()->updateOrCreate(['id' => $outcome['id'] ?? null], $outcome);
        }
        );
        $project->outcomes()->whereIn('id', $outcomes_to_remove)->delete(); // Delete outcomes.

        // Update Project managers and partners
        $project_owners = ProjectOwner::where('project_id', $project->id)->get();
        $project_partners = ProjectPartner::where('project_id', $project->id)->get();

        //Store new managers
        if ($request->user_id) {
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

        //Store new partners
        if ($request->partner_id) {
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
            foreach ($request->partner_id as $partner) {
                $new_partner = new ProjectPartner();
                $new_partner->project_id = $project->id;
                $new_partner->partner_id = $partner;
                $new_partner->save();
                //Give specific project permissions to partner
                $user = User::find($partner);
                $user->givePermissionTo('project-' . $project->id . '-list', 'project-' . $project->id . '-update', 'project-' . $project->id . '-edit');
            }
        }
        // If user logged in as partner, give him permission to edit project
        if (Auth::user()->hasRole('Partner')) {
            if ($project->project_owner()->count() == 0) {
                $project->project_owner()->create(['user_id' => 1]);
            }
            $new_partner = ProjectPartner::firstOrNew(['project_id' => $project->id, 'partner_id' => Auth::user()->id]);
            $new_partner->project_id = $project->id;
            $new_partner->partner_id = Auth::user()->id;
            $new_partner->save();
            Auth::user()->givePermissionTo('project-' . $project->id . '-list', 'project-' . $project->id . '-update', 'project-' . $project->id . '-edit');
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

        $admins = User::role(['Administrator', 'Program administrator'])->get();
        $areasIds = $project->areas->pluck('id')->toArray();
        $areasUsers = User::whereHas('areas', function ($query) use ($areasIds) {
            $query->whereIn('areas.id', $areasIds);
        })->get();

        $usersToNotify = empty($areasUsers) ? $admins : $areasUsers;

        if ($notifications['new_project']) {
            $usersToNotify->each(function ($admin) use ($project) {
                $admin->notify(new NewProjectRequest($project));
            });
            $project->project_partner->each(function ($partner) use ($project) {
                $partner->user->notify(new NewProjectRequest($project));
            });
        }
        if ($notifications['change_request']) {
            if ( $project->project_owner->isEmpty() || ($project->project_owner()->count() == 1 && $project->project_owner->pluck('user.id')->first() == 1) ) {
                $usersToNotify->each(function ($admin) use ($project) {
                    $admin->notify(new ProjectChangeRequest($project, $admin));
                });
            } else {
                $project->project_owner->each(function ($owner) use ($project) {
                    $owner->user->notify(new ProjectChangeRequest($project));
                });
            }
            $project->project_partner->each(function ($partner) use ($project) {
                $partner->user->notify(new ProjectChangeRequest($project));
            });
        }
        if ($notifications['project_updated']) {
            $project->project_owner->each(function ($owner) use ($project) {
                $owner->user->notify(new ProjectUpdated($project));
            });
            $project->project_partner->each(function ($partner) use ($project) {
                $partner->user->notify(new ProjectUpdated($project));
            });
        }

        return redirect()->route('project_show', $project);
    }

    public function accept(Project $project)
    {
        if ( !Auth::check() || !Auth::user()->hasRole(['Administrator', 'Spider']) || !Auth::user()->hasPermissionTo('project-' . $project->id . '-update')) {
            abort(403);
        }
        if ($project->object_type == 'project') {
            abort( response('Request error', 400) );
        }

        if ($project->object_type == 'project_change_request') {
            $mainProject = $project->main ?? Project::find($project->object_id);
            $mainProject->object_type = 'project_history';
            $mainProject->object_id = $project->id;
            $mainProject->save();
            $project->object_type = 'project';
            $project->object_id = null;
            $project->save();
            $project->refresh();
            $project->project_partner->each(function ($partner) use ($project) {
                $partner->user->notify(new ProjectChangeAccepted($project));
            });

        } elseif ($project->object_type == 'project_add_request') {
            $project->object_type = 'project';
            $project->save();
            $project->refresh();
            $project->project_partner->each(function ($partner) use ($project) {
                $partner->user->notify(new ProjectAccepted($project));
            });

        } else {
            abort( response('Request error', 400) );
        }

        $project->comments()->create([
            'user_id' => Auth::user()->id,
            'body' => sprintf('Project accepted by %s at %s and a notification has been sent to the partners.', Auth::user()->name, Carbon::now()->format('d-m-Y H:i:s')),
        ]);

        return redirect()->route('project_show', $project)->with('success', 'Project accepted and a notification has been sent to the partners.');

    }

    public function reject(Project $project)
    {
        if ( !Auth::check() || !Auth::user()->hasRole(['Administrator', 'Spider']) || !Auth::user()->hasPermissionTo('project-' . $project->id . '-update')) {
            abort(403);
        }
        if ($project->object_type == 'project') {
            abort( response('Request error', 400) );
        }

        if ($project->object_type == 'project_change_request') {
            $project->project_partner->each(function ($partner) use ($project) {
                $partner->user->notify(new ProjectChangeRejected($project));
            });

        } elseif ($project->object_type == 'project_add_request') {
            $project->project_partner->each(function ($partner) use ($project) {
                $partner->user->notify(new ProjectRejected($project));
            });
        } else {
            abort( response('Request error', 400) );
        }

        $project->comments()->create([
            'user_id' => Auth::user()->id,
            'body' => sprintf('Project rejected by %s at %s and a notification has been sent to the partners.', Auth::user()->name, Carbon::now()->format('d-m-Y H:i:s')),
        ]);

        return redirect()->route('project_show', $project)->with('success', 'Project rejected and a notification has been sent to the partners.');

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
        $projectupdate->internal_comment = request('internal_comment') ?? null;
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
        $outcome_update_array['outcome_completion'] = request('outcome_completion');

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
                $ou->completed_on = $outcome_update_array['outcome_completion'][$key] ? Carbon::now() : null;
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
        $output_update_array['progress'] = request('output_progress');

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
                    $data['progress'] = $output_update_array['progress'][$key];
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
                $outputupdate->progress = $output_update_array['progress'][$key];
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

        //Project Histories
        ProjectHistory::where('project_id', $project->id)->delete();

        //Outcomes
        OutcomeUpdate::where('project_update_id', $project->id)->delete();
        Outcome::where('project_id', $project->id)->delete();

        //Project Reminders
        ProjectReminder::where('project_id', $project->id)->delete();

        // Delete project
        $project->delete();
        return redirect()->route('project_home');
    }

    public function archive(Project $project)
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Spider', 'Administrator', 'Program administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-edit')) {
                abort(403);
            }
        }
        $project->object_type = 'project_archive';
        $project->update();
        return redirect()->route('project_show', $project);
    }

    public function unarchive(Project $project)
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Spider', 'Administrator', 'Program administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-edit')) {
                abort(403);
            }
        }
        $project->object_type = 'project';
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
