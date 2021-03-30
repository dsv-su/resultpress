<?php

namespace App\Console\Commands;

use App\Activity;
use App\Notifications\ActivityReminder;
use App\ProjectOwner;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\User;
use App\Project;
use Illuminate\Support\Facades\Notification;

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
                if($this->activity->end->toDateString() == $duedate && $this->activity->status() != 5)
                {
                    //Get Project
                    $this->project = Project::find($this->activity->project_id);
                    //Corresponing Managers
                    $this->project_owners = ProjectOwner::where('project_id', $this->project->id)->get();
                    foreach ($this->project_owners as $this->project_owner)
                    {
                        //Details for the email
                        $this->details = [
                            'title' => $this->activity->title,
                            'project' => $this->project->name,
                            'url' => url("/project/{$this->project->id}"),
                            'days' => $this->activity->reminder_due_days,
                        ];

                        $this->user = User::find($this->project_owner->user_id);
                        Notification::route('mail', $this->user->email)->notify(new ActivityReminder($this->details));
                    }
                }
            }


        }

        return 0;
    }
}
