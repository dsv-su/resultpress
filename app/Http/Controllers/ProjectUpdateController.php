<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Activity;
use App\ActivityUpdate;
use App\File;
use App\Output;
use App\OutputUpdate;
use App\Project;
use App\ProjectUpdate;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Project $project
     * @return Application|Factory|Response|View
     */
    public function index(Project $project)
    {
        return view('project.updates', ['project' => $project]);
        /*
        return view('project.updates', [
            'project' => $project,
            'project_updates' => ProjectUpdate::where('project_id', $project->id)->latest()->get()
        ]);
        */
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     */
    public function store_file(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param ProjectUpdate $project_update
     * @return Application|Factory|Response|View
     */
    public function show(ProjectUpdate $project_update)
    {
        $pus = ProjectUpdate::where('project_id', $project_update->project_id)->get();
        $index = 0;
        foreach ($pus as $key => $pu) {
            if ($pu->id == $project_update->id) {
                $index = $key + 1;
            }
        }
        $project_update->index = $index;

        // Grab files
        $files = File::where('filearea', 'project_update')->where('itemid', $project_update->id)->get() ?? null;
        foreach ($files as $file) {
            $file->path = Storage::url($file->filepath . '/' . $file->name);
        }

        return view('projectupdate.show', [
            'project_update' => $project_update,
            'project' => Project::where('id', $project_update->project_id),
            'activities' => Activity::where('project_id', $project_update->project_id)->get(),
            'outputs' => Output::where('project_id', $project_update->project_id)->get(),
            'activity_updates' => ActivityUpdate::where('project_update_id', $project_update->id)
                ->join('activities', 'activity_id', '=', 'activities.id')
                ->select('activity_updates.*', 'activities.title')
                ->get(),
            'output_updates' => OutputUpdate::where('project_update_id', $project_update->id)
                ->join('outputs', 'output_id', '=', 'outputs.id')
                ->select('output_updates.*', 'outputs.indicator', 'outputs.target')
                ->get(),
            'files' => $files,
            'review' => false
        ]);
    }

    /**
     * Show the form for reviewing the specified resource.
     *
     * @param ProjectUpdate $project_update
     * @return Application|Factory|Response|View
     */
    public function review(ProjectUpdate $project_update)
    {
        // Calculate things
        $activityupdates = ActivityUpdate::where('project_update_id', $project_update->id)
            ->join('activities', 'activity_id', '=', 'activities.id')
            ->select('activity_updates.*', 'activities.title')
            ->get();
        $outputupdates = OutputUpdate::where('project_update_id', $project_update->id)
            ->join('outputs', 'output_id', '=', 'outputs.id')
            ->select('output_updates.*', 'outputs.indicator', 'outputs.target')
            ->get();

        $pus = ProjectUpdate::where('project_id', $project_update->project_id)->get();
        $index = 0;
        foreach ($pus as $key => $pu) {
            if ($pu->id == $project_update->id) {
                $index = $key + 1;
            }
        }

        $activityupdates = $this->calculateActivities($activityupdates);
        $outputupdates = $this->calculateOutputs($outputupdates);
        $project_update->index = $index;

        return view('projectupdate.show', [
            'project_update' => $project_update,
            'project' => Project::where('id', $project_update->project_id),
            'activities' => Activity::where('project_id', $project_update->project_id)->get(),
            'outputs' => Output::where('project_id', $project_update->project_id)->get(),
            'activity_updates' => $activityupdates,
            'output_updates' => $outputupdates,
            'review' => true
        ]);
    }

    /**
     * Calculates outputs progress.
     *
     * @param $outputupdates
     * @return Response
     */
    public function calculateOutputs($outputupdates)
    {
        foreach ($outputupdates as $ou) {
            $contributionstring = 'Contributes ';
            $totalstring = 'Output is ';
            $output = Output::where('id', $ou->output_id)->first();
            $alloutputupdates = OutputUpdate::where('output_id', $output->id)->get();
            $totalcontribution = 0;
            foreach ($alloutputupdates as $aou) {
                $totalcontribution += $aou->value;
            }

            $outputcontribution = number_format(($ou->value / $output->target) * 100) . '%';
            $totalcontribution = number_format(($totalcontribution / $output->target) * 100) . '%';

            $contributionstring .= $outputcontribution . ' of target.';
            $totalstring .= $totalcontribution . ' done.';

            $ou->contributionstring = $contributionstring;
            $ou->totalstring = $totalstring;
        }

        return $outputupdates;
    }

    /**
     * Calculates budget and timing based on activities data.
     *
     * @param $activityupdates
     * @return Response
     */
    public function calculateActivities($activityupdates)
    {
        foreach ($activityupdates as $au) {
            $deadlinestring = 'Activity ';
            $budgetstring = 'Activity ';
            $activity = Activity::where('id', $au->activity_id)->first();
            $allactivityupdates = ActivityUpdate::where('activity_id', $activity->id)->get();
            $totalmoneyspent = 0;
            foreach ($allactivityupdates as $aau) {
                $totalmoneyspent += $aau->money;
            }

            $remainingpercentage = number_format(abs(1 - ($totalmoneyspent / $activity->budget)) * 100) . '%';
            $moneyspent = ' ' . abs($activity->budget - $totalmoneyspent) . ' (' . $remainingpercentage . ')';

            if ($au->status == 3) {
                $deadlinestring .= ' was completed';
                $budgetstring .= ' was completed';
                if ($totalmoneyspent > $activity->budget) {
                    $budgetstring .= $moneyspent . ' over bugdet';
                } else if ($totalmoneyspent < $activity->budget) {
                    $budgetstring .= $moneyspent . ' under budget';
                } else {
                    $budgetstring .= ' on budget';
                }
            } else {
                $deadlinestring .= ' is';
                $budgetstring .= ' has';
                if ($totalmoneyspent > $activity->budget) {
                    $budgetstring .= $moneyspent . ' exceeding budget';
                } else if ($totalmoneyspent <= $activity->budget) {
                    $budgetstring .= $moneyspent . ' remaining';
                }
            }
            if ($activity->start->gt($au->date)) {
                $deadlinestring .= ' ahead of schedule';
            } elseif ($activity->end->lt($au->date)) {
                $deadlinestring .= ' behind of schedule';
            } else {
                $deadlinestring .= ' on schedule';
            }
            $au->budgetstring = $budgetstring;
            $au->deadlinestring = $deadlinestring;
        }

        return $activityupdates;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProjectUpdate $project_update
     * @return RedirectResponse
     */
    public function update(ProjectUpdate $project_update)
    {
        $project_update->internal_comment = request('internal_comment');
        $project_update->partner_comment = request('partner_comment');
        $project_update->approved = request('approved');
        $project_update->save();
        return redirect()->route('projectupdate_index', $project_update->project_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProjectUpdate $project_update
     * @return void
     * @throws \Exception
     */
    public function destroy(ProjectUpdate $project_update)
    {
        // Delete associated updates
        ActivityUpdate::where('project_update_id', $project_update->id)->delete();
        OutputUpdate::where('project_update_id', $project_update->id)->delete();
        $project_update->delete();
    }
}
