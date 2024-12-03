<?php

namespace App\Console\Commands;

use App\Models\Auth\User;
use App\Services\Grow\SyncService;
use App\Services\Metrc\MetrcRequestService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefreshMetrcGrow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grow:syncMetrc {userId} {--daysBack=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh/sync grow data from METRC';



    protected $syncService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->syncService = new SyncService();
        $this->api = new MetrcRequestService();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        $this->user = User::where('id', $userId)->with('location')->first();
        if(!$this->user) {
            $this->error("User does not exist"); exit;
        }
        Auth::guard('api')->setUser($this->user);


        if ($this->confirm('Do you want to delete for '.$this->user->location->name.' - yes or no?')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $lid = $this->user->location->id;
            $this->line(DB::DELETE('DELETE FROM grow_strains WHERE location_id = ?', [$lid]) . ' strains deleted');
            $this->line(DB::DELETE('DELETE FROM grow_rooms WHERE location_id = ?', [$lid]) . ' rooms deleted');
            $this->line(DB::DELETE('DELETE FROM grow_items WHERE location_id = ?', [$lid]) . ' items deleted');
            $this->line(DB::DELETE('DELETE FROM grow_plants WHERE location_id = ?', [$lid]) . ' plants deleted');
            $this->line(DB::DELETE('DELETE FROM grow_plant_batches WHERE location_id = ?', [$lid]) . ' plant batches deleted');
            $this->line(DB::DELETE('DELETE FROM grow_harvests WHERE location_id = ?', [$lid]) . ' harvests deleted');
            $this->line(DB::DELETE('DELETE FROM grow_packages WHERE location_id = ?', [$lid]) . ' packages deleted');
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }//end confirm

            $this->syncRooms();
            $this->syncStrains();
            $this->syncPlantBatches();
            $this->syncPlants();
            $this->syncHarvests();
            $this->syncPackages();

    }

    protected function syncRooms() {
        $this->warn('Syncing rooms....');
        $metrcRooms = $this->api->getActiveRooms($this->user);
        $roomSynced = 0;
        foreach($metrcRooms as $room) {
            try {
                $this->syncService->importMetrcRoom((array)$room, $this->user);
                $roomSynced++;
            }catch(\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        $this->alert($roomSynced. ' rooms synced');
    }

    protected function syncStrains() {
        $this->warn('Syncing strains...');
        $metrcStrains = $this->api->getActiveStrains($this->user);
        $strainSynced = 0;
         foreach($metrcStrains as $strain) {
             try {
             $this->syncService->importMetrcStrain((array)$strain,$this->user);
             $strainSynced++;
             }catch(\Exception $e) {
                 $this->error($e->getMessage());
             }
         }
        $this->alert($strainSynced. ' strains synced');
    }

    protected function syncPlants() {
        $this->warn('Syncing plants...');
        $api = new MetrcRequestService();

        $startDate = Carbon::now()->subDays($this->option('daysBack'));
        $endDate = Carbon::tomorrow();

        $plantsSynced = 0;
        // loop through plants one day at a time
        while ($startDate->lessThan($endDate)) {
            //$this->line($startDate->toIso8601String());
            $start = $startDate->toISOString();
            $startPlusOne = $startDate->addDay()->toISOString();

            $this->line($start." - ".$startPlusOne);

            $vegPlants = $api->getVegetativePlants($this->user, $start, $startPlusOne);
            $floweringPlants = $api->getFloweringPlants($this->user, $start, $startPlusOne);
            $this->line(count($vegPlants).' veg + '.count($floweringPlants).' flower');
            foreach($vegPlants as $plant) {
                try{
                $this->syncService->importMetrcPlant((array)$plant,$this->user);
                $plantsSynced++;
                }catch(\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            foreach($floweringPlants as $plant) {
                try {
                $this->syncService->importMetrcPlant((array)$plant,$this->user);
                $plantsSynced++;
                }catch(\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
        $this->alert($plantsSynced.' plants synced');

    }

    protected function syncPlantBatches() {
        $this->warn('Syncing plant batches...');
        $api = new MetrcRequestService();

        $startDate = Carbon::now()->subDays($this->option('daysBack'));
        $endDate = Carbon::tomorrow();

        $batchesSynced = 0;
        // loop through plants one day at a time
        while ($startDate->lessThan($endDate)) {
            $start = $startDate->toISOString();
            $startPlusOne = $startDate->addDay()->toISOString();

            $this->line($start." - ".$startPlusOne);

            $activeBatches = $api->getActivePlantBatches($this->user, $start, $startPlusOne);
            $inactiveBatches = $api->getInactivePlantBatches($this->user, $start, $startPlusOne);
            $this->line(count($activeBatches).' active + '.count($inactiveBatches).' inactive');
            foreach($activeBatches as $batch) {
                try {
                $this->syncService->importMetrcPlantBatch((array)$batch,$this->user);
                $batchesSynced++;
                }catch(\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            foreach($inactiveBatches as $batch) {
                try {
                $this->syncService->importMetrcPlantBatch((array)$batch,$this->user);
                $batchesSynced++;
                }catch(\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
        $this->alert($batchesSynced.' batches synced');

    }

    protected function syncHarvests() {
        $this->warn('Syncing harvests...');
        $api = new MetrcRequestService();

        $startDate = Carbon::now()->subDays($this->option('daysBack'));
        $endDate = Carbon::tomorrow();

        $harvestSynced = 0;
        // loop through plants one day at a time
        while ($startDate->lessThan($endDate)) {
            $start = $startDate->toISOString();
            $startPlusOne = $startDate->addDay()->toISOString();

            $this->line($start." - ".$startPlusOne);

            $activeHarvest = $api->getActiveHarvests($this->user, $start, $startPlusOne);
            $inactiveHarvest = $api->getInactiveHarvests($this->user, $start, $startPlusOne);
            $this->line(count($activeHarvest).' active + '.count($inactiveHarvest).' inactive');
            foreach($activeHarvest as $harvest) {
                try {
                $this->syncService->importMetrcHarvest((array)$harvest,$this->user);
                $harvestSynced++;
                }catch(\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            foreach($inactiveHarvest as $harvest) {
                try {
                $this->syncService->importMetrcHarvest((array)$harvest,$this->user);
                $harvestSynced++;
                }catch(\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
        $this->alert($harvestSynced.' harvest synced');

    }

    protected function syncPackages() {
        $this->warn('Syncing packages...');
        $api = new MetrcRequestService();

        $startDate = Carbon::now()->subDays($this->option('daysBack'));
        $endDate = Carbon::tomorrow();

        $packageSynced = 0;
        // loop through plants one day at a time
        while ($startDate->lessThan($endDate)) {
            $start = $startDate->toISOString();
            $startPlusOne = $startDate->addDay()->toISOString();

            $this->line($start." - ".$startPlusOne);

            $activePackage = $api->getActivePackages($this->user, $start, $startPlusOne);
            $inactivePackage = $api->getInactivePackages($this->user, $start, $startPlusOne);
            $this->line(count($activePackage).' active + '.count($inactivePackage).' inactive');
            foreach($activePackage as $package) {
                try {
                $this->syncService->importMetrcPackage((array)$package,$this->user);
                $packageSynced++;
                }catch(\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            foreach($inactivePackage as $package) {
                try {
                $this->syncService->importMetrcPackage((array)$package,$this->user);
                $packageSynced++;
                }catch(\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }
        $this->alert($packageSynced.' packages synced');

    }
}
