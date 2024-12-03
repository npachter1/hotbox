<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;

use App\Models\Auth\Location;

use App\Services\Dispensary\TaxService;

use App\Helpers\Generator;

use Auth;
use DB;
use File;


class Housekeep extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sysadmin:housekeep';

    /**S
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nightly Housekeeping';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;


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
        $this->line('Initializing Housekeeping Routine for '.config('app.name'));
        $this->line('--------------------------------------------------------');

    
        /* Per-Location Management*/
        $this->line('Location maintenence starting..');
        Location::chunk(20, function ($locations) {
            foreach($locations as $location){
        
                // HINT: add nightly stuff you want to do here..





        
            }
        });






        /* TODO sync taxrates with a state entity, or from a repository */



        
        /* return sucess */
        $this->line('Housekeeping done, Have a great day!');
        //$this->line('Installation Complete');
        //$this->info('Installation Complete');
        //$this->comment('Installation Complete');
        //$this->question('Installation Complete');
        //$this->error('Installation Complete');

    }

}
