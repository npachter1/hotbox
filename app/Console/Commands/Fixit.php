<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;

use App\Models\Auth\User;
use App\Models\Auth\Location;

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
use App\Models\Auth\Addressbook;
use App\Models\Dispensary\Customer;

use App\Services\Dispensary\CategoryService;
use App\Services\Dispensary\InventoryService;
use App\Services\Vendors\BiotrakDataService;
use App\Services\Metrc\MetrcRequestService;

use App\Helpers\LocationScope;
use App\Helpers\Generator;
use App\Helpers\Util;

use Auth;
use DB;
use Carbon\Carbon;
use File;
use Exception;
use Log;


class Fixit extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sysadmin:fixit';

    /**S
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Webmaster Fixit Tool';

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
        
        $this->line('------------- FixIt routine Starting.. -----------------------------------');

        $this->line('HotBox Dev Scratchpad..');

        $user = User::find('43039dd9-f89a-4c30-83c1-c1b8e4b90bf6'); //superadmin
        Auth::guard('api')->setUser($user); // set suites prime user - for scoped data sets
        $this->line('Logged into '.$user->name);
           
        // TEST SHIT..
        
        
        
        
        
        
        
        
        /* backfill settled_at for sales */
        /*$this->line('Backfilling sale settled_at dates');
        Sale::whereIn('status',['settled','refunded'])->whereNull('settled_at')->chunk(100,function($sales){
            foreach($sales as $sale){
                $sale->settled_at = $sale->created_at;
                $sale->save();
                $this->comment(' -- Backfilled settled_at date for '.$sale->order_number);
            }
        });*/
        

        /*foreach(Product::get() as $prod)
            $prod->save();*/
        
        /*foreach(Inventory::withoutGlobalScope(LocationScope::class)->select('id','item_strain')->whereNotNull('item_strain')->get() as $inv){
            foreach(InventoryStrain::where('name','like',$inv->item_strain)->get() as $str){
                $this->comment(' -- Updating strain '.$str->name.' to '.$inv->item_strain);
                $str->name = $inv->item_strain;
                $str->save();
            }
        }*/
        
        /*foreach(Category::withoutGlobalScope(LocationScope::class)->get() as $cat){
            if(in_array($cat->equivalency_type,['noncannabis','misc','merchandise'])) $cat->equivalency_type = Category::_parseCategoryType(null,$cat->name,'metrc');
            $cat->save();
            $this->comment(' -- updating '.$cat->name.' to '.$cat->equivalency_type);
        }*/

        /* test parsed potency */
        /*foreach(Category::with('products.activeInventory')->where('equivalency_type','edible')->ofActive()->get() as $cat){
            foreach($cat->products as $prod){
                foreach($prod->activeInventory as $inv){
                    $wp = \App\Models\Dispensary\ReceivingPackage::_getParsedPotencyAmount($inv->item_name);
                    $inv->weight_potency = $wp;
                    $inv->save();
                    $this->comment(' -- got weight_potency of '.$wp.' for '.$inv->item_name);
                }
            }
        }*/
        
        //$user->location->discounts()->sync([1,2,3,5,7,8]);
        //$user->location->discounts()->sync([1,2,3,8,15]);
        
        /* update Equiv values.. */
        /*foreach(Category::withoutGlobalScope(LocationScope::class)->whereNotNull('settings')->get() as $cat){
            if($cat->contains_thc===false) $cat->equivalency_type = 'noncannabis';
            elseif(data_get($cat->settings,'thc_equiv_ratio',null)==1) $cat->equivalency_type = 'flower';
            elseif(data_get($cat->settings,'thc_equiv_ratio',null)==3.5) $cat->equivalency_type = 'concentrate';
            elseif(data_get($cat->settings,'thc_equiv_ratio',null)==35) $cat->equivalency_type = 'edible';
            else $cat->equivalency_type = 'noncannabis';
            
            $cat->settings = null;
            $cat->save();
            $this->comment(' -- updating '.$cat->name.' to '.$cat->equivalency_type);
        }*/
        
        
        /* reindex strains */
        /*$invServe = new InventoryService();
        foreach(Inventory::select('item_strain')->whereNotNull('item_strain')->whereNull('archived_at')->groupBy('item_strain')->get() as $row){
            $success = $invServe->_saveStrain($row->item_strain);
            Inventory::where('item_strain',$row->item_strain)->update(['item_strain'=>strtolower($row->item_strain)]);
            $this->comment(' -- '.($success===true ? 'Updated '.$row->item_strain.' in index' : 'Skipped '.$row->item_strain));  
        }*/
        
        //DB::table('dispensary_inventories')->where('location_id',99999)->where('quantity_on_hand','<=',0)->update(['archived_at' => Carbon::now()->toDateTimeString()]);
        //DB::table('dispensary_categories')->where('location_id',99999)->update(['deleted_at' => null]);
        
       /*foreach(Category::withoutGlobalScope(LocationScope::class)->get() as $cat){
           $cat->metrc_category_type = (($cat->metrc_category_type && $cat->metrc_category_type!='') ? $cat->metrc_category_type : 'merchandise');
           $cat->save();
       }
       foreach(Priceset::withoutGlobalScope(LocationScope::class)->get() as $ps){
           $ps->category_type = ((($type = Category::withoutGlobalScope(LocationScope::class)->where('id',$ps->category_type)->first()) != null) ? $type->metrc_category_type : 'merchandise');
           $ps->save();
       }*/

        
        
       /* category types to category migrate */    
       /*foreach(Category::withoutGlobalScope(LocationScope::class)->get() as $cat){
           if(($type = CategoryMetrc::withoutGlobalScope(LocationScope::class)->where('id',$cat->metrc_category_type)->first()) != null){
               $cat->metrc_category_type = $type->product_category_type;
               $cat->metrc_category_id = $type->id;
               $cat->public_img = $cat->public_img ?: $type->public_img;
               $cat->contains_thc = true;
               $cat->settings = (object)[
                   'thc_equiv_prompt'   => $type->thc_equiv_prompt,
                   'thc_equiv_ratio'    => $type->thc_equiv_ratio
                ];
           }else{
               $cat->metrc_category_type = null;
               $cat->contains_thc = false;
               $cat->settings = (object)[
                   'thc_equiv_prompt'   => 'amount',
                   'thc_equiv_ratio'    => 0
                ];
           }
           
            $cat->save();
            $this->comment(' -- Updated '.$cat->name.' as type '.$cat->metrc_category_type);  
       }*/
        
        
        
        /*foreach(Inventory::with('product')->get() as $inv){
                $inv->save();
                $this->comment(' -- added name to '.$inv->item_barcode.' - '.$inv->item_name);  
        }*/
        
        
        /*foreach(Product::with('inventory')->whereHas('inventory')->get() as $prod){
            if($prod->inventory->where('archived_at',null)->count()<=0){
                $prod->archived_at = Carbon::now()->toDateTimeString();
                $this->comment(' -- archived produt '.$prod->name.' cause all inventory was arhived');  
                $prod->save();
            }
        }*/
        
        
        /*$count = 0;
        $this->info('Saving all sales to get all aggregate data..');
        Sale::where('customer_id','!=',1)->chunk(20,function($sales)use(&$count){
            foreach($sales as $sale){
                $sale->save();
                $count++;
            }
        $this->comment(' -- '.$count.' sales reaggregated..');  
        });
        $this->line('Sales reaggregated for '.$user->location_id);*/
        
        
        /* recon all inventory */
        /*$invService = new \App\Services\Dispensary\InventoryService();
        $count = 0;
        Inventory::with('product')->where('quantity_adjust','!=',0)->chunk(100,function($invs)use(&$count){
            foreach($invs as $item){
    		    $item->quantity_on_hand = (float)($item->quantity_received - $item->quantity_sold + $item->quantity_adjust);
    		    $item->save();
    		    if($item->product) $item->product->save();
                $count++;
            }
        $this->comment(' -- '.$count.' IInventory adjs..');  
        });
        */$this->line('Inv adj reaggregated for '.$user->location_id);

        
        /* return sucess */
        $this->line('FixIt Routine done.');
        //$this->line('Installation Complete');
        //$this->comment('Installation Complete');
        //$this->question('Installation Complete');
        //$this->error('Installation Complete');

    }

}
