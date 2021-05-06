<?php

namespace App\Console\Commands;

use App\Activity;
use App\Project;
use App\ProjectOwner;
use App\ProjectPartner;
use App\ProjectReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ProjectDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends reminder when deadline due';
    protected $project_reminders, $project_reminder, $old_reminder, $delayed_reminder;
    protected $check_activity, $check_activities;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get all the reminders
        $this->project_reminders = ProjectReminder::all();
        // Set old deadline
        $this->delayed_reminder = new ProjectReminder();
        // Scan through deadlines
        foreach($this->project_reminders as $this->project_reminder) {
            $duedate = Carbon::now()->addDays($this->project_reminder->reminder_due_days)->toDateString();
            // Check if a notification should be sent
            if($this->project_reminder->set->toDateString() == $duedate and $this->project_reminder->reminder == true) {
                // Deadline - Trigger notification reminder
                // Get project
                $this->project = Project::find($this->project_reminder->project_id);

                //Check for delayed activities
                $this->old_reminders = ProjectReminder::where('project_id', $this->project->id)->where('set', '!=', $this->project_reminder->set->toDateString())->get();
                foreach($this->old_reminders as $this->old_reminder) {
                    //Get activities of old reminder
                    $this->check_activities = $this->project->activities()->get();
                    foreach($this->check_activities as $this->check_activity) {
                        //Check that delayed activities are not marked as completed or archived
                        if($this->check_activity->status() != 5 and $this->check_activity->status() != 6) {
                            //Check that activities end are within project start and current deadline
                            if ($this->check_activity->end->between($this->project->start, $this->project_reminder->set)) {
                                //Store old deadline
                                $this->delayed_reminder = $this->old_reminder;
                            }
                        }
                    }
                } // end check old deadlines

                //Send notification reminder to all project owners
                $projectowners = ProjectOwner::with('user')->where('project_id', $this->project->id)->get();
                foreach($projectowners as $projectowner) {
                    Mail::to($projectowner->user->email)->send(new \App\Mail\ProjectDeadlines(Project::find($this->project_reminder->project_id), $this->project_reminder, $this->delayed_reminder));
                }
                //Send notification reminder to all project partners
                $projectpartners = ProjectPartner::with('user')->where('project_id', $this->project->id)->get();
                foreach($projectpartners as $projectpartner) {
                    Mail::to($projectpartner->user->email)->send(new \App\Mail\ProjectDeadlines(Project::find($this->project_reminder->project_id), $this->project_reminder, $this->delayed_reminder));
                }

            }

        }

        return 0;
    }
}
