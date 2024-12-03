<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;

use App\Models\Auth\User;
use App\Models\Auth\Location;
use App\Models\Dispensary\Campaign;
use App\Models\Dispensary\CampaignLog;

use App\Services\Dispensary\CampaignService;


use App\Helpers\Generator;
use App\Helpers\Util;

use Auth;
use DB;
use Carbon\Carbon;
use File;


class Quarters extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sysadmin:quarters';

    /**S
     * The console command description.
     *
     * @var string
     */
    protected $description = 'HeVERY 4 HR Jobs';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    protected $user;


    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     *
     * @internal param Filesystem $filesystem
     */
    public function __construct(Filesystem $files){
        
        parent::__construct();
        $this->files = $files;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        
        $this->line('--------------------------------------------------------');
        $this->line('Initializing 4Hourl Background Jobs Dispatcher for '.config('app.name'));
        $this->line('--------------------------------------------------------');

    
        /* Per-Location Management*/
        $this->line('Location maintenence starting..');
        Auth::guard('api')->setUser(User::find('43039dd9-f89a-4c30-83c1-c1b8e4b90bf6'));
        $this->user = Auth::guard('api')->user();



        /* TODO update customer groups */



        
        
        
        
        Location::where('status','activated')->chunk(20, function ($locations) {
            foreach($locations as $location){
                
                $this->line('Starting 4 Hr Tasks for '.$location->name);



            }
        });

        
        /* return sucess */
        $this->line('4Hour Jobs done, Have a great day!');
        //$this->line('Installation Complete');
        //$this->info('Installation Complete');
        //$this->comment('Installation Complete');
        //$this->question('Installation Complete');
        //$this->error('Installation Complete');

    }

}
