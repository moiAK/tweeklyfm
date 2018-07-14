<?php namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TestCommand extends Command
{

    use DispatchesJobs;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tweekly:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is a test command.';

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
     * @return mixed
     */
    public function fire()
    {
        $user = User::find(1);
        $connection = $user->connections()->where("network_name", "=", "twitter")->first();

        $job = (new \App\Jobs\Connection\PublishToTwitter($user, $connection, "You people are animals."))
                    ->onQueue('publish.twitter');

        $this->dispatch($job);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [

        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [

        ];
    }
}
