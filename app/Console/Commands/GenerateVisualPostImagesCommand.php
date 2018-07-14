<?php

/*
 * This file is part of tweeklyfm/tweeklyfm
 *
 *  (c) Scott Wilcox <scott@dor.ky>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GenerateVisualPostImagesCommand extends Command
{
    use DispatchesJobs;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tweekly:generate:visual-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will generate visual post images.';

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
        $total = 0;
        $users = User::all();
        foreach ($users as $user) {
            $job = (new \App\Jobs\Premium\GenerateVisualPost($user))->onQueue('premium.visual-posts');
            $this->dispatch($job);
            $total++;
        }

        $this->info('Generated a total of '.$total.' visual post images');
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
