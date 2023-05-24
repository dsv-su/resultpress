<?php

namespace App\Console\Commands;

use App\Outcome;
use App\ProjectReminder;
use App\Output;
use App\Project;
use App\Activity;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class FixSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slugs:rebuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild slugs for a model or models';

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
        $model = $this->choice(
            'What models you want to rebuild slugs for?',
            ['All', Project::class, Activity::class, Outcome::class, Output::class, ProjectReminder::class],
            0,
            $maxAttempts = null,
            $allowMultipleSelections = true
        );

        $force = $this->confirm('Do you wish to force the rebuild?');

        if ($model[0] == 'All') {
            $this->info('Rebuilding slugs for all models');
            $this->newLine(3);
            $this->rebuildSlugs(Project::class, $force);
            $this->rebuildSlugs(Activity::class, $force);
            $this->rebuildSlugs(Outcome::class, $force);
            $this->rebuildSlugs(Output::class, $force);
            $this->rebuildSlugs(ProjectReminder::class, $force);
        } else {
            foreach ($model as $m) {
                if ($m !== 'All') {
                    $this->rebuildSlugs($m, $force);
                    $this->newLine(3);
                }
            }
        }
        $this->info('Done');
        return 0;
    }

    private function rebuildSlugs($model, $force)
    {
        $this->info('Rebuilding slugs for ' . $model);

        $model::withoutGlobalScopes()->get()->each(function ($model) use ($force) {
            $name = $model->name ?? $model->title ?? $model->indicator;
            if (function_exists('mb_substr')) {
                $name = mb_substr($name, 0, 50);
            } else {
                $name = substr($name, 0, 50);
            }
    
            $slug = Str::slug($name);
            $exists = $model::where('slug', $slug)->exists();
            if ($exists) {
                $slug = $slug . '-' . $model->id;
            }

            if(! $exists) {
                $model->slug = $slug;
            }

            if ($force && $exists) {
                $model->slug = $slug;
            }
    
            $this->info('Created slug: ' . $slug);
            $model->save();
        });
        $this->newLine(2);
    }
}
