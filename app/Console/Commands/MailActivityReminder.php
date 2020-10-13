<?php

namespace App\Console\Commands;

use App\Activity;
use App\ActivityUpdate;
use App\Mail\ActivityReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\User;
use App\Project;
use Mail;

class MailActivityReminder extends Command
{
    public $activity, $activity_updates, $activity_update;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email reminder for activity due';

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
        //Get all activities
        $this->activities = Activity::all();


        foreach ($this->activities as $this->activity)
        {
            if ($this->activity->reminder == true)
            {
                $duedate = Carbon::now()->addDays($this->activity->reminder_due_days)->toDateString();
                if($this->activity->end->toDateString() == $duedate)
                {
                    // Retrive all activity updates for the matching activity
                    if($this->activity_update = ActivityUpdate::where('activity_id', $this->activity->id)->latest()->first())

                        //Check if activity has been flagged done
                        if ($this->activity_update->status == 1 || $this->activity_update->status == 2)
                        {
                            //Get Project
                            $this->project = Project::find($this->activity->project_id);
                            //Corresponing Manager
                            $this->user = User::find($this->project->user_id);
                            //Details for the email
                            $this->details = [
                                'title' => $this->activity->title,
                                'project' => $this->project->name,
                            ];
                            Mail::to($this->user->email)->send(new ActivityReminder($this->details));
                        }


                }
            }


        }

        return 0;
    }
}
