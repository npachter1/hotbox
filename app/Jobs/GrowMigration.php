<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\Metrc\MetrcRequestService;

use App\Models\Auth\User;
use App\Models\Auth\Location;

use App\Models\Dispensary\CategoryMetrc;
use App\Services\Dispensary\CategoryService;
use App\Services\Grow\RoomService;
use App\Services\Grow\StrainService;
use App\Services\Grow\ItemService;
use App\Services\Grow\PlantBatchService;
use App\Services\Grow\PlantService;
use App\Services\Grow\HarvestService;
use App\Services\Grow\PackageService;
use DateTime;
use Carbon\Carbon;

use Auth;
use stdClass;


class GrowMigration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;
    public $timeout = 300; // TODO we may need more then 5 mins!
    protected $location;
    protected $user;
    protected $metrcApi;

      /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Location $location, User $user){
        
        $this->location = $location;
        $this->user = $user;
        $this->user->location_id = $location->id;                               // as this unserializes the User, they may have changed locations, and scoped data sets take thier current location, so
                                                                                // here we just assign the passed location id to the user for this job's authentication purpose 
        $this->metrcApi = new MetrcRequestService();

        $this->onQueue('migrate');
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(){

        Auth::guard('api')->setUser($this->user);                               // Authenticate passsed user (with passed location_id from contruct) - this will get us access to globally scoped data sets (ie: Helpers/LocationScope)

        $backfill = new DateTime(date('Y-m-d', strtotime(data_get($this->user->location,'migration_settings.backfill','2019-01-01'))));
        $endDate = new DateTime(date('Y-m-d'));

        $hasMetrcCats = CategoryMetrc::where('location_id', $this->user->location_id)->get();
        if ($hasMetrcCats) {
        	$categoryService = new CategoryService;
        	try {
            	$categoryService->syncMetrcCategories($this->user,'job');               
        	} catch(\Exception $e) {
            	throw new \Exception('Could not establish Metrc connection');
        	}
        } else {
            $metrc_cats = $this->metrcApi->getItemCategories($this->user);
            foreach($metrc_cats as $cat) {
                $hb_metrc_category = new CategoryMetrc;
                $hb_metrc_category->location_id = $this->current_location['id'];
                $hb_metrc_category->name = $cat->Name;
                $hb_metrc_category->product_category_type = $cat->ProductCategoryType;
                $hb_metrc_category->quantity_type = $cat->QuantityType;
                $hb_metrc_category->requires_strain = $cat->RequiresStrain;
                $hb_metrc_category->requires_item_brand = $cat->RequiresItemBrand;
                $hb_metrc_category->requires_administration_method = $cat->RequiresAdministrationMethod;
                $hb_metrc_category->requires_unit_cbd_percent = $cat->RequiresUnitCbdPercent;
                $hb_metrc_category->requires_unit_cbd_content = $cat->RequiresUnitCbdContent;
                $hb_metrc_category->requires_unit_thc_percent = $cat->RequiresUnitThcPercent;
                $hb_metrc_category->requires_unit_thc_content = $cat->RequiresUnitThcContent;
                $hb_metrc_category->requires_unit_volume = $cat->RequiresUnitVolume;
                $hb_metrc_category->requires_unit_weight = $cat->RequiresUnitWeight;
                $hb_metrc_category->requires_serving_size = $cat->RequiresServingSize;
                $hb_metrc_category->requires_supply_duration_days = $cat->RequiresSupplyDurationDays;
                $hb_metrc_category->requires_ingredients = $cat->RequiresIngredients;
                $hb_metrc_category->requires_product_photo = $cat->RequiresProductPhoto;
                $hb_metrc_category->can_contain_seeds = $cat->CanContainSeeds;
                $hb_metrc_category->can_be_remediated = $cat->CanBeRemediated;
                $hb_metrc_category->thc_equiv_ratio = $this->equivs[$cat->ProductCategoryType];
                $hb_metrc_category->thc_equiv_prompt = $this->prompts[$cat->ProductCategoryType];
                $hb_metrc_category->save();
            }
        }

        // clear existing location specific data 
        DB::table('grow_packages')->where('location_id', $this->location->id)->delete();
        DB::table('grow_harvests')->where('location_id', $this->location->id)->delete();
        DB::table('grow_plants')->where('location_id', $this->location->id)->delete();
        DB::table('grow_plant_batches')->where('location_id', $this->location->id)->delete();
        DB::table('grow_items')->where('location_id', $this->location->id)->delete();
        DB::table('grow_strains')->where('location_id', $this->location->id)->delete();
        DB::table('grow_rooms')->where('location_id', $this->location->id)->delete();

        // migrate Rooms
        $metrcRooms = $this->metrcApi->getActiveRooms($this->user);
        $service = new RoomService;
        foreach($metrcRooms as $room) {
        	$service->storeMetrc((array)$room, $this->user);
        }

        // migrate Strains
        $metrcStrains = $this->metrcApi->getActiveStrains($this->user);
        $service = new StrainService;
        foreach($metrcStrains as $strain) {
        	$service->storeMetrc((array)$strain, $this->user);
        }

        // migrate Items
        $metrcItems = $this->metrcApi->getActiveItems($this->user);
        $service = new ItemService;
        foreach($metrcItems as $item) {
        	$service->storeMetrc((array)$item, $this->user);
        }   

        // migrate Plant Batches
        $service = new PlantBatchService;
        $startDate = new DateTime(date('Y-m-d', strtotime(data_get($this->user->location,'migration_settings.backfill','2019-01-01'))));
        $plusOneDay = null;
        if ($startDate) {
            $plusOneDay = new DateTime(date('Y-m-d', strtotime(data_get($this->user->location,'migration_settings.backfill','2019-01-01'))));
            $plusOneDay->modify('+1 day');
        }
        while($plusOneDay <= $endDate) {
        	$metrcPlantBatches = $this->metrcApi->getActivePlantBatches($this->user, $startDate->format('Y-m-d'), $plusOneDay->format('Y-m-d'));
            if ($metrcPlantBatches) {
                foreach($metrcPlantBatches as $metrcPlantBatch) {
                	$service->storeMetrc((array)$metrcPlantBatch, $this->user);
                }
            }
            $metrcPlantBatches = $this->metrcApi->getInactivePlantBatches($this->user, $startDate->format('Y-m-d'), $plusOneDay->format('Y-m-d'));
            if ($metrcPlantBatches) {
                foreach($metrcPlantBatches as $metrcPlantBatch) {
                	$service->storeMetrc((array)$metrcPlantBatch, $this->user);
                }
            }
        	if ($startDate) {
                $startDate = clone $plusOneDay;
                $plusOneDay->modify('+1 day');
            } else
                break;
		}     

        // migrate Plants
        $service = new PlantService;
        $startDate = new DateTime(date('Y-m-d', strtotime(data_get($this->user->location,'migration_settings.backfill','2019-01-01'))));
        $plusOneDay = null;
        if ($startDate) {
            $plusOneDay = new DateTime(date('Y-m-d', strtotime(data_get($this->user->location,'migration_settings.backfill','2019-01-01'))));
            $plusOneDay->modify('+1 day');
        }
        // loop through plants one day at a time
        while($plusOneDay <= $endDate) {
            // get vegetative plants
            $metrcPlants = $this->metrcApi->getVegetativePlants($this->user, $startDate->format('Y-m-d'), $plusOneDay->format('Y-m-d'));
            if ($metrcPlants) {
                foreach($metrcPlants as $metrcPlant) {
                    $service->storeMetrc((array)$metrcPlant, $this->user);
                }
            }
            // get flowering plants
            $metrcPlants = $this->metrcApi->getFloweringPlants($this->user, $startDate->format('Y-m-d'), $plusOneDay->format('Y-m-d'));
            if ($metrcPlants) {
                foreach($metrcPlants as $metrcPlant) {
                    $service->storeMetrc((array)$metrcPlant, $this->user);
                }
            }
            // get inactive plants
            $metrcPlants = $this->metrcApi->getInactivePlants($this->user, $startDate->format('Y-m-d'), $plusOneDay->format('Y-m-d'));
            if ($metrcPlants) {
                foreach($metrcPlants as $metrcPlant) {
                    $service->storeMetrc((array)$metrcPlant, $this->user);
                }
            }
            if ($startDate) {
                $startDate = clone $plusOneDay;
                $plusOneDay->modify('+1 day');
            } else
                break;
        }       

        // migrate Harvests
        $service = new HarvestService;
        $startDate = new DateTime(date('Y-m-d', strtotime(data_get($this->user->location,'migration_settings.backfill','2019-01-01'))));
        $plusOneDay = null;
        if ($startDate) {
            $plusOneDay = new DateTime(date('Y-m-d', strtotime(data_get($this->user->location,'migration_settings.backfill','2019-01-01'))));
            $plusOneDay->modify('+1 day');
        }
        // loop through harvests one day at a time
        while($plusOneDay <= $endDate) {
            $metrcHarvests = $this->metrcApi->getActiveHarvests($this->user, $startDate->format('Y-m-d'), $plusOneDay->format('Y-m-d'));
            if ($metrcHarvests) {
                foreach($metrcHarvests as $metrcHarvest) {
                    $service->storeMetrc((array)$metrcHarvest, $this->user);
                }
            }
            $metrcHarvests = $this->metrcApi->getInactiveHarvests($this->user, $startDate->format('Y-m-d'), $plusOneDay->format('Y-m-d'));
            if ($metrcHarvests) {
                foreach($metrcHarvests as $metrcHarvest) {
                    $service->storeMetrc((array)$metrcHarvest, $this->user);
                }
            }
            if ($startDate) {
                $startDate = clone $plusOneDay;
                $plusOneDay->modify('+1 day');
            } else
                break;
        }       

        // migrate Packages
        $service = new PackageService;
        $startDate = new DateTime(date('Y-m-d', strtotime(data_get($this->user->location,'migration_settings.backfill','2019-01-01'))));
        $plusOneDay = null;
        if ($startDate) {
            $plusOneDay = new DateTime(date('Y-m-d', strtotime(data_get($this->user->location,'migration_settings.backfill','2019-01-01'))));
            $plusOneDay->modify('+1 day');
        }
        // loop through harvests one day at a time
        while($plusOneDay <= $endDate) {
            $metrcPackages = $this->metrcApi->getActivePackages($this->user, $startDate->format('Y-m-d\TH:i:s\Z'), $plusOneDay->format('Y-m-d\TH:i:s\Z'));
            if ($metrcPackages) {
                foreach($metrcPackages as $metrcPackage) {
                    $service->storeMetrc((array)$metrcPackage, $this->user);
                }
            }
            $metrcPackages = $this->metrcApi->getInactivePackages($this->user, $startDate->format('Y-m-d\TH:i:s\Z'), $plusOneDay->format('Y-m-d\TH:i:s\Z'));
            if ($metrcPackages) {
                foreach($metrcPackages as $metrcPackage) {
                    $service->storeMetrc((array)$metrcPackage, $this->user);
                }
            }
            if ($startDate) {
                $startDate = clone $plusOneDay;
                $plusOneDay->modify('+1 day');
            } else
                break;
        }

        return true;
    }

}