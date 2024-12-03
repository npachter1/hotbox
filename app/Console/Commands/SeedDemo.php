<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;

use App\Models\Auth\Location;
use App\Models\Auth\User;
use App\Models\Dispensary\Category;
use App\Models\Dispensary\CategoryMetrc;
use App\Models\Dispensary\Product;
use App\Models\Dispensary\Inventory;
use App\Models\Dispensary\InventoryLog;
use App\Models\Dispensary\InventoryStrain;
use App\Models\Dispensary\Priceset;
use App\Models\Dispensary\Sale;
use App\Models\Dispensary\SaleItem;
use App\Models\Dispensary\Drawer;
use App\Models\Dispensary\Receiving;
use App\Models\Dispensary\ReceivingPackage;
use App\Models\Auth\Addressbook;
use App\Models\Dispensary\Customer;

use App\Services\Dispensary\TaxService;

use App\Helpers\Generator;

use Auth;
use Carbon\Carbon;
use DB;
use File;


class SeedDemo extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sysadmin:seeddemo';

    /**S
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed Demo Data';

    protected $files;
    protected $user;
    protected $location;
    protected $service;


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
        $this->line('Initializing Demo Data Seeder for '.config('app.name'));
        $this->line('--------------------------------------------------------');

        $this->user = User::find('43039dd9-f89a-4c30-83c1-c1b8e4b90bf6');

        DB::table('dispensary_campaigns')->whereNotNull('archived_at')->delete();
        DB::table('dispensary_inventories')->whereNotNull('archived_at')->delete();
        DB::table('dispensary_receivings')->whereNotNull('archived_at')->delete();
        DB::table('dispensary_pricesets')->whereNotNull('archived_at')->delete();
        DB::table('dispensary_groups')->whereNotNull('archived_at')->delete();
        DB::table('dispensary_discounts')->whereNotNull('archived_at')->delete();
        $this->info('Removed Archived Campaigns, Inventory, Receiving, pricesets, Groups and Discounts.');


        /* Per-Location Management*/
        Location::chunk(20, function ($locations) {
            foreach($locations as $location){

                $this->line('Seeding demo data for '.$location->name);
                $this->user->location_id = $location->id;
                Auth::guard('api')->setUser($this->user);

                $invCount = 0;
                foreach(Inventory::whereNotNull('expires_at')->get() as $inv){
                    $inv->expires_at = Carbon::now()->addDays(rand(15,360))->toDateTimeString();
                    $inv->save();
                    $invCount++;
                }
                $this->info('Shuffled '.$invCount.' expires_at dates between 15 and 360 days from now.');
                
                $saleCount = 0;
                foreach(Sale::with('items','payments')->whereHas('customer')->get() as $sale){
                    $date = Carbon::now()->subDays(rand(1,120))->toDateTimeString(); // move sale - and all assoc data to random date in past 4 months to keep analyitics fresh
                    
                    $sale->created_at = $date;
                    $sale->settled_at = $date;
                    
                    foreach($sale->items as $item){
                        $item->created_at = $date;
                        $item->save();
                    }
                    
                    foreach($sale->payments as $pay){
                        $pay->created_at = $date;
                        $pay->save();
                    }
                    
                    
                    $sale->save();
                    $saleCount++;
                    $this->comment(' -- '.$sale->order_number.' and all assoc data now has a date of '.$date);
                }
                $this->info('Shuffled '.$saleCount.' Sales and all related data date between 2 and 120 days since now.');


                // 



                
                $this->line('Done with '.$location->name);
        
            }
        });




        
        /* return sucess */
        $this->line('Demo Data is Ready..');
        //$this->line('Installation Complete');
        //$this->info('Installation Complete');
        //$this->comment('Installation Complete');
        //$this->question('Installation Complete');
        //$this->error('Installation Complete');

    }

}
