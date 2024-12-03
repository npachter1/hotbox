<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\Auth\Location;

use Auth;
use stdClass;


class UpdateLocations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $location;
    public $tries = 3;
    public $timeout = 180;
    
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Location $suite){
        
        $this->location = $suite;
        
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){
        
        Auth::guard('api')->setUser($this->location->prime);                    // set locations prime user - for scoped data sets
        
        // update location data..
        
        
        
        return true;
    }
    
    
    /* the queing tag for */
    public function tags(){
        return ['UpdateLocationData - '.$this->location->id];
    }
    
    
}