<?php

namespace App\Console\Commands;

use App\Models\Auth\Location;
use App\Models\Grow\Snapshot;
use Illuminate\Console\Command;

class GrowSnapshotGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grow:snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes nightly snapshot of grow';

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
    public function handle()
    {
        //TODO:Quick fix for Flower Island. Weights will need conversions to grams
        $growLocations = Location::where('type','grow')->get();

        foreach($growLocations as $loc) {
            $snapshot = new Snapshot;
            $snapshot->location_id = $loc->id;
            $plantBatches = \DB::select('SELECT count(*) as total_batches, sum(count) as total_immature  FROM grow_plant_batches where 
                                                deleted_at is null and count > 0 and location_id = ? ',[$loc->id]);


            $snapshot->plant_batch_count = data_get($plantBatches[0],'total_batches',0);
            $snapshot->immature_plant_count = data_get($plantBatches[0], 'total_immature',0);

            $immature = \DB::select('SELECT  sum(count) as aggregate, type  FROM grow_plant_batches where 
                                                deleted_at is null and count > 0 and location_id = ? group by 2',[$loc->id]);

            foreach($immature as $i) {
                (strtolower($i->type) == 'seed') ? $snapshot->seed_count = data_get($i,'aggregate',0)
                        : $snapshot->clone_count = data_get($i,'aggregate',0);
            }


            $mature = \DB::select("SELECT growth_phase as phase, count(label) as aggregate FROM grow_plants WHERE deleted_at is null 
                    AND location_id = ? and state = 'Tracked' group by growth_phase",[$loc->id]);

            $snapshot->mature_plant_count = 0;
            foreach($mature as $m) {
                $snapshot->mature_plant_count += $m->aggregate;
                (strtolower($m->phase) == 'flowering') ? $snapshot->flowering_plant_count = data_get($m,'aggregate',0) :
                        $snapshot->vegetative_plant_count = data_get($m,'aggregate',0);
            }



            $harvests = \DB::select("SELECT count(name) as total_harvests, sum(current_weight) as total_current_weight,
                                sum(total_trim_weight) as total_trim_weight, sum(total_waste_weight) as total_waste_weight
                                FROM grow_harvests WHERE deleted_at is null AND location_id = ? AND finished_at is null 
                                ",[$loc->id]);

            $snapshot->harvest_count = data_get($harvests[0],'total_harvests',0);
            $snapshot->harvest_total_current_weight = data_get($harvests[0],'total_current_weight',0);
            $snapshot->harvest_total_trim_weight = data_get($harvests[0],'total_trim_weight',0);
            $snapshot->harvest_total_waste_weight = data_get($harvests[0],'total_waste_weight',0);




            $packages = \DB::select("SELECT count(*) as package_count, sum(quantity) as package_total_weight FROM grow_packages 
                    WHERE deleted_at is null AND location_id = ? AND transfer_id is null",[$loc->id]);

            $snapshot->package_count = data_get($packages[0],'package_count',0);
            $snapshot->package_total_weight = data_get($packages[0],'package_total_weight',0);

            try {
                $snapshot->save();
            } catch(\Exception $e) {
                $this->alert($e->getMessage());
            }


        }
    }
}
