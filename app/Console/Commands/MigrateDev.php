<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;

use App\Mail\UserMigrated;

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
use App\Models\Dispensary\ReceivingPackage;
use App\Models\Auth\Addressbook;
use App\Models\Dispensary\Customer;

use App\Services\Dispensary\CategoryService;
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
use Mail;


class MigrateDev extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sysadmin:migratedev';
    /**S
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispensary Migration Dev/test';
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
     
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
    public function __construct(Filesystem $files,User $user){
        
        parent::__construct();
        $this->files = $files;
        
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        
        $this->line('------------- Starting the Migration of Dispensary Dev/Test -----------------------------------');

        Auth::guard('api')->setUser(User::find('43039dd9-f89a-4c30-83c1-c1b8e4b90bf6')); // Authenticate passsed user (with passed location_id from contruct) - this will get us access to globally scoped data sets (ie: Helpers/LocationScope)
        $this->user = Auth::guard('api')->user();
        $this->user->location_id = $this->ask('Location to Migrate? (hint: 99999');
        if(($this->location = Location::find($this->user->location_id))==null){ $this->error('No location was found - aborting'); return false; }    

        $this->service = new BiotrakDataService();



        // replace below code with jobs/BioTrakMigration when ready.. 
        /* ###################################### Start BioTrakMigration Job ################################ */
        
        $jobline = [];
        $categoryService = new CategoryService;
        $this->location->status = 'migrating';
        $this->location->save();

        $btLocId = data_get($this->location,'migration_settings.btdb_location_id',null);
        $backfill = Carbon::parse(data_get($this->user->location,'migration_settings.backfill','2019-01-01 00:00:01'));
        $stateName = data_get($this->location,'migration_settings.btdb_state_name','Colorado');


        /* We have to clear existing location specific data */
        DB::table('dispensary_customer_queues')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_rewards')->whereIn('customer_id',Customer::where('location_id',$this->location->id)->select('id')->pluck('id')->all())->delete();
        DB::table('dispensary_rewards')->where('customer_id',1)->delete();
        DB::table('auth_addressbook')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_sale_payments')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_sale_items')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_sales')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_pricesets')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_inventories')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_inventory_logs')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_products')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_categories')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_customers')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_receivings')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_drawer_events')->where('location_id', $this->location->id)->delete();
        DB::table('dispensary_drawers')->where('location_id', $this->location->id)->delete();



        /* Scope of data */
        $custHash = [];                                                         // key/value sets of old id => new id for data insertion
        $vendHash = [];
        $catHash = [];
        $typeHash = [];
        $catTypeHash = [];
        $prodPriceSync = [];
        $prodHash = [];
        $prodNameHash = [];
        $prodCatHash = [];
        $invHash = [];
        $invNopeHash = [];
        $gradeHash = [];
        $saleHash = [];
        $discHash = [];
        
        // get the customers, categories, products, and inventory sold since last backfill
        $prodScope = $this->service->from('sales')->select('productid')->where('location', $btLocId)->where('datetime', '>=', strtotime($backfill))->groupBy('productid')->pluck('productid')->toArray();
        $invScope = $this->service->from('sales')->select('inventoryid')->where('location', $btLocId)->where('datetime', '>=', strtotime($backfill))->groupBy('inventoryid')->pluck('inventoryid')->toArray();
        $stockScope = $this->service->from('inventory')->select('id')->where('location', $btLocId)->where('remainingweight','>',0)->pluck('id')->toArray();
        $stockProdScope = $this->service->from('inventory')->select('productid')->where('location', $btLocId)->where(function($q)use($backfill,$invScope){
                $q->where('remainingweight','>',0)->orWhereIn('id',$invScope);
        })->pluck('productid')->toArray();
    
        $catScope = $this->service->from('products')->select('productcategory')->where('location', $btLocId)->where('deleted',0)->where(function($q)use($backfill,$stockProdScope){
            $q->where('created', '>=',$backfill)->orWhereIn('id',$stockProdScope);
        })->groupBy('productcategory')->pluck('productcategory')->toArray();

        $custScope = $this->service->from('sales')->select('customerid')->where('location', $btLocId)->where(function($q)use($backfill,$stockScope){
            $q->where('datetime', '>=', strtotime($backfill))->orWhereIn('inventoryid',$stockScope);
        })->groupBy('customerid')->pluck('customerid')->toArray();


        /* 1. establish default model records.. */
        $defAdr = Addressbook::create([
            'location_id'       => $this->location->id,
            'type'              => 'vendor',
            'name'              => 'Misc Vendor',
            'address1'          => '123 Main St',
            'city'              => 'Anywhere',
            'region'            => 'CO',
            'country'           => 'US',
            'zip'               => '80205'
        ]);

        $defCust = new Customer;
        $defCust->location_id = $this->location->id;
        $defCust->addressbook_id = $defAdr->id;
        $defCust->type = 'recreational';
        $defCust->first_name = 'Migration';
        $defCust->last_name = 'Non-Entered';
        $defCust->birthdate = Carbon::createFromTimestamp(-1577913694)->toDateTimeString();
        $defCust->gender = 'male';
        $defCust->created_at = Carbon::now()->toDateTimeString();
        $defCust->save();

        $defCat = new Category;
        $defCat->name = 'Misc Migrated';
        $defCat->contains_thc = 0;
        $defCat->notes = 'Migrated '.Carbon::now()->toDateTimeString();
        
        $defCat->equivalency_type = 'noncannabis';
        $defCat->created_at = Carbon::now()->toDateTimeString();
        $defCat->save();
                
        $defProd = new Product;
        $defProd->category_id = $defCat->id;
        $defProd->name = 'Misc Migrated';
        $defProd->description = 'Migrated '.Carbon::now()->toDateTimeString();
        $defProd->nature_type = 'noncannabis';
        $defProd->created_at = Carbon::now()->toDateTimeString();
        $defProd->save();
        
        
        $hbRec = Receiving::create([                                            // create receiving PO for new inventory
            'invoice_ref'       => 'BT-MIGRATE-'.Carbon::now()->format('mdy'),
            'transfer_ref'      => 'BT-MIGRATE-'.Carbon::now()->format('mdy'),
            'po_number'         => 'BT-MIGRATE-'.Carbon::now()->format('mdy')
        ]);
        
        $hbRec->type = 'custom';
        $hbRec->addressbook_id = $defAdr->id;
        $hbRec->status = 'received';
        $hbRec->archived_at = Carbon::now()->toDateTimeString();
        $hbRec->save();
        
        
        $hbDraw = [];
        for($i=1; $i<=12; $i++){
            $hbDraw[$i] = new Drawer;        
            $hbDraw[$i]->user_id = $this->user->id;
            $hbDraw[$i]->name = 'MIGRATED-HISTORY-'.$i;
            $hbDraw[$i]->total_sales = 0;
            $hbDraw[$i]->current_balance = 0;
            $hbDraw[$i]->save();
        }
        
        $jobline[] = '1. Location Specific data Cleared.  Initializing Fresh Migration..';
$this->info('1. Location Specific data Cleared.  Initializing Fresh Migration..');
        
        $mcatCount = 0;
        foreach($this->service->from('inventorytypes')->where('limittypeid','!=',0)->where('deleted', 0)->get() as $btTyp){
            if(($btEquiv = $this->service->from('equivalency_by_type')->where('limittype_id',$btTyp->limittypeid)->where('state',$stateName)->orderBy('id','desc')->first()) != null){
                
                $typeHash[$btTyp->id] = (object)[
                    'public_img'    => data_get(Util::getImagery(),data_get($btEquiv,'limittype_name','Buds'),null),
                    'category_type' => data_get($btTyp,'name','Misc Type'),
                    'equivalency_type'  => Category::_parseCategoryType(null,data_get($btEquiv,'limittype_name','Buds'),data_get($this->location->settings,'regulatory_agent','metrc')) ?: 'noncannabis'
                ];
                
                $mcatCount++;
            }
        }
        
        $jobline[] = '2. Synced '.$mcatCount.' product types';
$this->info('2. Synced '.$mcatCount.' product types');

        /* 3 Customers registry */
        $custCount = 0;
        $this->service->from('customers')->where('location', $btLocId)->where('deleted',0)->where(function($q)use($backfill,$custScope){
            $q->where('created','>=',$backfill)->orWhereIn('customerid',$custScope);
        })->select(
            'customerid','location','birthyear','birthmonth','birthday','licenseexpyear','licenseexpmonth','firstname','middlename','lastname','address1','address2','city','state','zip','points',
            'cell','email','phone','licensenum','emailoptin','smsoptin','amountspent','redcard','redcard','redcardtime','plantcount','iscaregiver','md_metrc_num_of_plants','sex','md_metrc_patient_id','modified','created'
        )->orderBy('created','desc')->orderBy('modified','desc')->chunk(100,function($btCusts)use(&$custHash,&$custCount){
            foreach($btCusts as $btCust){

                if(($birth = strtotime(data_get($btCust,'birthyear',null).'-'.data_get($btCust,'birthmonth',null).'-'.data_get($btCust,'birthday',null)))===false) $birth = -1577913694; // 99 yrs old - theres currupt data, strtotime just returns false if we cant get right birthday from their structure
                elseif(($expiry = strtotime(data_get($btCust,'licenseexpyear',null).'-'.data_get($btCust,'licenseexpmonth',null).'-01'))===false) $expiry = 1861929506; // Jan 1 2029

                $hbSettings = (object)[                                         // To be used if we are using existing points migration setting
                    'migration_id'      => data_get($btCust,'customerid',null),
                    'migration_points'  => data_get($btCust,'points',0)
                ];

                if(($btRedCard = data_get($btCust, 'redcard', null))!=null){    // Medical patients vs recreational type
                    $mmj_card = data_get($btCust, 'redcard', null);
                    $mmj_card_state = data_get($btCust,'state','CO');           // TODO close enough for migration, but may want to confirm
                    $mmj_card_expiry_date = Carbon::createFromTimestamp(data_get($btCust,'redcardtime',0))->toDateTimeString();
                    
                    data_set($hbSettings,'med_carry_weight',data_get($btCust,'md_metrc_num_of_plants',2),true); // TODO assuming a medical limit of 2g as def
                    data_set($hbSettings,'med_plant_count',data_get($btCust,'plantcount',null),true);
                    
                    $type = (data_get($btCust, 'iscaregiver', null) == 1 ? 'caregiver' : 'patient'); 
                }else{
                    $mmj_card = null;
                    $mmj_card_state = null;
                    $mmj_card_expiry_date = null;
                    
                    $type = 'recreational';
                }
                
                $hbAdr = Addressbook::where('address1',data_get($btCust, 'address1', null))->where('email',data_get($btCust, 'email', null))->whereNotNull('address1')->orderBy('created_at','desc')->first() ?: new Addressbook;
                $hbAdr->location_id = $this->location->id;
                $hbAdr->third_party_id  = null;
                $hbAdr->type = 'consumer';
                $hbAdr->name = preg_replace('/  /',' ',data_get($btCust, 'firstname', null).' '.data_get($btCust, 'middlename', null).' '.data_get($btCust, 'lastname', null));
                $hbAdr->address1 = data_get($btCust, 'address1', null);
                $hbAdr->address2 = data_get($btCust, 'address2', null);
                $hbAdr->city = data_get($btCust, 'city', null);
                $hbAdr->region = data_get($btCust, 'state', null);
                $hbAdr->country = 'US'; // ok for now, like were pretty much US based..
                $hbAdr->county = null;
                $hbAdr->zip = data_get($btCust, 'zip', null);
                $hbAdr->phone = (data_get($btCust, 'cell', null)) ? data_get($btCust, 'cell', null) : data_get($btCust, 'phone', null);
                $hbAdr->contact_notes = null;
                $hbAdr->email = data_get($btCust, 'email', null);
                $hbAdr->licensenum = null;
                $hbAdr->website = null;
                $hbAdr->industry =  'consumer';
                $hbAdr->created_at = Carbon::createFromTimestamp(strtotime(data_get($btCust,'created','')))->toDateTimeString();
                $hbAdr->save();

                $hbCust = new Customer;
                $hbCust->location_id = $this->location->id;
                $hbCust->addressbook_id = $hbAdr->id;
                $hbCust->metrc_id = data_get($btCust,'md_metrc_patient_id',null);
                $hbCust->type = $type;
                $hbCust->first_name = data_get($btCust, 'firstname', null);
                $hbCust->last_name = data_get($btCust, 'lastname', null);
                $hbCust->middle_name = data_get($btCust, 'middlename', null);
                $hbCust->birthdate = Carbon::createFromTimestamp($birth)->toDateTimeString();
                $hbCust->gender = (data_get($btCust,'sex',0)==1 ? 'female' : 'male');
                $hbCust->drivers_license = data_get($btCust, 'licensenum', null);
                $hbCust->drivers_license_state = data_get($btCust, 'state', null);
                $hbCust->drivers_license_expiry_date = Carbon::createFromTimestamp($expiry)->toDateTimeString();
                $hbCust->mmj_card = $mmj_card;
                $hbCust->mmj_card_state = $mmj_card_state;
                $hbCust->mmj_card_expiry_date = $mmj_card_expiry_date;
                $hbCust->sms_optin = ((data_get($btCust, 'emailoptin', null)==1 && $hbAdr->phone) ? true : false);
                $hbCust->email_optin = ((data_get($btCust, 'smsoptin', null)==1 && $hbAdr->email) ? true : false);
                $hbCust->settings = $hbSettings;
                $hbCust->total_reward_points = (data_get($this->location,'migration_settings.migrate_rewards_option',null)=='transfer' ? (float)data_get($btCust,'points',0) : 0);
                $hbCust->total_spent = (float)data_get($btCust, 'amountspent',0);
                $hbCust->created_at = Carbon::createFromTimestamp(strtotime(data_get($btCust,'created','')))->toDateTimeString();
                $hbCust->save();


                $custHash[$btCust->customerid] = $hbCust->id;
                $custCount++;

            }
$this->comment(' -- migrated chunk of 100 customers - '.$custCount);
        });

        $jobline[] = '3 Successfully Migrated Last '.number_format($custCount).' Customers with a registered birthdate / license expiry, and last bought or last modified since '.$backfill->format('m/d/y');
$this->info('3 Successfully Migrated Last '.number_format($custCount).' Customers with a registered birthdate / license expiry, and last bought or last modified since '.$backfill->format('m/d/y'));

        /* 4 Vendors */
        $vendCount = 0;
        $this->service->from('vendors')->where('deleted',0)->select(
            'id','name','address1','address2','city','state','zip','phone','deleted','licensenum','created','modified','email','website','description','contact','mip','medical'
        )->orderBy('created','desc')->chunk(100,function($btVends)use(&$vendHash,&$vendCount){
            foreach($btVends as $btVend){
                
                $hbAdr = Addressbook::where('name',$btVend->name)->where('address1',$btVend->address1)->orderBy('created_at','desc')->first() ?: new Addressbook;
                $hbAdr->location_id = $hbAdr->location_id ?: $this->location->id;
                $hbAdr->third_party_id  = null;
                $hbAdr->type = 'vendor';
                $hbAdr->name = $btVend->name;
                $hbAdr->address1 = $btVend->address1;
                $hbAdr->address2 = $btVend->address2;
                $hbAdr->city = $btVend->city;
                $hbAdr->region = $btVend->state;
                $hbAdr->country = $btVend->zip;
                $hbAdr->county = 'US'; // ok for now, like were pretty much US based..
                $hbAdr->zip = $btVend->zip;
                $hbAdr->phone = $btVend->phone;
                $hbAdr->contact_notes = ($btVend->contact ? $btVend->contact.' ' : null).$btVend->description;
                $hbAdr->email = $btVend->email;
                $hbAdr->licensenum = $btVend->licensenum;
                $hbAdr->website = $btVend->website;
                $hbAdr->industry =  ($btVend->mip==1 ? 'mip' : ($btVend->medical==1 ? 'medical' : 'retail'));
                $hbAdr->created_at = Carbon::createFromTimestamp(strtotime($btVend->created))->toDateTimeString();
                $hbAdr->save();
    
                $vendHash[$btVend->id] = $hbAdr->id;
                $vendCount++;
            
            }
$this->comment(' -- migrated chunk of 100 vendors - '.$vendCount);
        });

        $jobline[] = '4. Successfully Migrated Last '.number_format($vendCount).' Vendors';
$this->info('4. Successfully Migrated Last '.number_format($vendCount).' Vendors');

        /* 5 Iterate through categories sold since backfill or still in stock */
        $catCount = 0;
        $this->service->from('productcategories')->orderBy('modified','desc')->chunk(100,function($btCats)use(&$catHash,&$catCount){
            foreach($btCats as $btCat){
                $hbCat = new Category;
                $hbCat->name = data_get($btCat,'name',null);
                
                $hbCat->contains_thc = (data_get($btCat,'ismedicated',0)==1 ? true : false);
                $hbCat->equivalency_type = Category::_parseCategoryType(null,$hbCat->name,data_get($this->location->settings,'regulatory_agent','metrc'));
                $hbCat->public_img = Util::guessImagery($hbCat->name);
                $hbCat->notes = 'Migrated '.Carbon::now()->toDateTimeString();
                
                
                $hbCat->created_at = Carbon::createFromTimestamp(strtotime(data_get($btCat,'created','')))->toDateTimeString();
                $hbCat->archived_at = ($btCat->deleted==1 ? Carbon::now()->toDateTimeString() : null);
                
                $hbCat->save();
                $catCount++;
                $catHash[$btCat->id] = $hbCat->id;
            }
        });
        
        $jobline[] = '5. Successfully Migrated Last '.number_format($catCount).' Categories';
$this->info('5. Successfully Migrated Last '.number_format($catCount).' Categories');


        /* 6 The products that sold since backlfill or still in stock */
        $prodCount = 0;
        $this->service->from('products')->select(
                'id','name','taxcategory','productcategory','strain','pricepoint','image','location','ismedicated','requiresweighing','requiresinventory','deleted','productdescription',
                'inventorytype','created','modified','defaultvendor','prepack','metrc_item_name'
        )->whereIn('id',$stockProdScope)->orderBy('modified')->chunk(100,function($btProds)use(&$prodHash,&$prodCatHash,&$prodNameHash,&$prodCount,$catHash,$defCat){
           foreach($btProds as $btProd){
               
                $hbProd = Product::where('name',data_get($btProd, 'name', null))->orderBy('created_at','desc')->first() ?: new Product;
                $hbProd->category_id = data_get($catHash,$btProd->productcategory,$defCat->id);
                $hbProd->name = data_get($btProd, 'name', null);
                $hbProd->description = data_get($btProd, 'productdescription', null);
                $hbProd->nature_type = (data_get($btProd,'ismedicated',1)==1 ? (data_get($this->location->settings,'is_medical',false)===true ? 'medical' : 'recreational') : 'noncannabis');
                $hbProd->public_img = data_get($btProd,'image',null);
                
                $hbProd->created_at = Carbon::createFromTimestamp(strtotime(data_get($btProd,'created','')))->toDateTimeString();
                $hbProd->archived_at = ($btProd->deleted==1 ? Carbon::now()->toDateTimeString() : null);
                
                $hbProd->save();
                $prodCount++;
                $prodHash[$btProd->id] = $hbProd->id;
                
                if(isset($catHash[$hbProd->category_id])){                      // update category equivalency default based on prod ismedicated (in case theres no inventory to map its type later..) 
                    $cat = Category::find($catHash[$hbProd->category_id]);
                    $cat->contains_thc = (data_get($btProd,'ismedicated',0)==1 ? true : false);
                    $cat->equivalency_type = (data_get($btProd,'ismedicated',0)==1 ? 'flower' : 'noncannabis');
                    $cat->save();
                }
                

                $prodNameHash[$btProd->id] = $hbProd->name;
                $prodCatHash[$btProd->id] = data_get($catHash,$btProd->productcategory,'XXX');
                
           }
        });

        $jobline[] = '6. Successfully Migrated '.$prodCount.' Products ';
$this->info('6. Successfully Migrated '.$prodCount.' Products ');


        /* 7 map the active or sold since backfill inventory  */
        $invCount = 0;
        $this->service->from('inventory')->select(
            'id','strain','straintype','weight','dispensed','dispensedsofar','transactionid','adjustedsofar','remainingweight','productid','ismedicated','requiresweighing','quantity','vendorid','deleted',
            'inventorytype','grade','batchno','inventoryparentid','secondaryid','currentroom','expiration','cost_per_unit','custom_1','expiration_timestamp','created'
        )->where('location',$btLocId)->where(function($q)use($backfill,$invScope){
            $q->where('remainingweight','>',0)->orWhere('created','>=',$backfill)->orWhereIn('id',$invScope);
        })->orderBy('created','desc')->chunk(100,function($btInvs)use(&$invCount,&$invHash,&$catTypeHash,&$gradeHash,&$prodPriceSync,&$invNopeHash,$prodHash,$prodNameHash,$prodCatHash,$typeHash,$vendHash,$defAdr,$hbRec,$defProd){
            foreach($btInvs as $btInv){

                $hbInv = new Inventory;
                $hbInv->product_id           = data_get($prodHash,$btInv->productid ?: 'XXXXX',null) ?: $defProd->id;
                $hbInv->addressbook_id       = data_get($vendHash,($btInv->vendorid ?: 'XXXXX'),null) ?: $defAdr->id; // vendor id hashed to our addressbook
                $hbInv->receiving_id         = $hbRec->id;                  // migration default receiving
                $hbInv->cost_unit            = data_get($btInv,'cost_per_unit',null) ?: 0.01;
                    
                /* 8 insert pricesets of inventory by grade if not exist */
                if($btInv->grade){
                    if(!isset($gradeHash[$btInv->grade]) || !isset($hbPriceset)){
                        $btGrade = $this->service->from('inventorygrading')->where('id',$btInv->grade)->first();                                  
                        $hbPriceset = Priceset::find(data_get($gradeHash,$btInv->grade,'XXXXX')) ?: new Priceset;
                        $hbPriceset->category_type = data_get($typeHash,$btInv->inventorytype.'.category_type','merchandise');
                        $hbPriceset->name_grade = data_get($btGrade,'name','Default priceset');
                        $hbPriceset->type_uom = 'g';

                        $uom = 'g';
                        $used = [];
                        $hbPriceset->amount_tiers = collect(explode(':',preg_replace('/;/',':',data_get($btGrade,'pricepoint',''))))->map(function($item,$key)use(&$uom,&$used){
                           $uom = (data_get($item,'2','g')=='g' ? 'g' : 'ea');
                           $data = explode(',', $item);

                           if($key==0) return null;
                           elseif(isset($used[data_get($data,1,'XXXXX')])) return null;
                           else{
                                $used[data_get($data,1,'XXXXX')] = 1;
                                return (object)[
                                   'amount' => (float)data_get($data,'1',1),
                                   'price'  => round(((float)data_get($data,'0',1)/((float)data_get($data,'1',1) ?: 1)),4)
                                ];
                            }
                        })->filter()->sortBy('amount')->values()->toArray();

                        $hbPriceset->type_uom = $uom;
                        $hbPriceset->amount_default = data_get($hbPriceset->amount_tiers,'0.price',0.02);
                        $hbPriceset->is_default = 0;
                        $hbPriceset->rank = 99;
                        $hbPriceset->notes_announcement = 'Migrated from Biotrak on '.carbon::now()->toDateTimeString();

                        $hbPriceset->save();
                        $gradeHash[data_get($btGrade,'id','XXXXX')] = $hbPriceset->id;
                        $prodPriceSync[$hbInv->product_id][$hbPriceset->id] = 1;
                    }else $prodPriceSync[$hbInv->product_id][$hbPriceset->id] = 1;

                    $hbInv->priceset_id          = $hbPriceset->id;
                    $hbInv->retail_unit          = $hbPriceset->amount_default;            
                }else{
                    $hbInv->priceset_id          = null;
                    $hbInv->retail_unit          = $hbInv->cost_unit*2;     // TODO find fixed retaikl price for non grade/pricesets
                }
                    
                $hbInv->metrc_tag            = data_get($btInv,'secondaryid',null);
                $hbInv->item_barcode         = data_get($btInv,'id',null) ?: Generator::genBarcode();
                $hbInv->item_batch           = data_get($btInv,'batchno',null);
                $hbInv->item_strain          = substr(data_get($btInv,'strain',null),0,175);
                $hbInv->item_notes           = data_get($btInv,'custom_1',null); // could be helpfull
                $hbInv->room_ref             = data_get($btInv,'currentroom',null);
            
            
                /* store strain in InventoryStrain registry */
                if($hbInv->item_strain && ($strain = InventoryStrain::where('name',strtolower(($hbInv->item_strain || '')))->first())==null){
                    $strain = new InventoryStrain;
                    $strain->name = $hbInv->item_strain;
                    $strain->notes = 'Migrated '.Carbon::now()->toDateTimeString();
                    $strain->save();
                }
                
                
                /* parse potency in g from custom_a and/or product name */
                preg_match('/THC: *(\d*\.?\d{1,4})%/',data_get($btInv,'custom_1',null),$thc);
                $potencyPct = (($btInv->requiresweighing==0 && $btInv->ismedicated==1) ? (float)$btInv->weight : ((float)data_get($thc,'1',(ReceivingPackage::_getParsedPotencyAmount(data_get($prodHash,$btInv->productid ?: 'XXXXX',null))*100))/1000));
                
                if(data_get($btInv,'requiresweighing',1)==1){
                    $hbInv->quantity_received    = (float)data_get($btInv,'weight',0);
                    $hbInv->quantity_requested   = (float)data_get($btInv,'weight',0);
                    $hbInv->quantity_sold        = (float)data_get($btInv,'dispensedsofar',0);
                    //$hbInv->quantity_on_hand     = (float)data_get($btInv,'remainingweight',0); // this seems very inconsistant in the bt database..
                    $hbInv->quantity_on_hand     = ($hbInv->quantity_received - $hbInv->quantity_sold);
                    $hbInv->quantity_adjust      = (float)data_get($btInv,'adjustedsofar',0); // this seems to always be 0 in biotrak..
                    $hbInv->unit_of_measure      = 'g'; // are assuming all weighable in grams at moment
                    $hbInv->amount_unit          = 1;
                    $hbInv->weight_potency       = $potencyPct;
                }else{                                                          // prepackaged
                    $hbInv->quantity_received    = (float)data_get($btInv,'quantity',0);
                    $hbInv->quantity_requested   = (float)data_get($btInv,'quantity',0);
                    $hbInv->quantity_sold        = (float)data_get($btInv,'dispensedsofar',0);
                    //$hbInv->quantity_on_hand     = (float)data_get($btInv,'remainingweight',0); // this seems very inconsistant in the bt database..
                    $hbInv->quantity_on_hand     = ($hbInv->quantity_received - $hbInv->quantity_sold);
                    $hbInv->quantity_adjust      = (float)data_get($btInv,'adjustedsofar',0); // this seems to always be 0 in biotrak..
                    $hbInv->unit_of_measure      = 'ea';
                    $hbInv->amount_unit          = 1; // if requiresweighing is 0, the weight input is the amount per unit of qty.
                    $hbInv->weight_potency       = $potencyPct;
                }

                $hbInv->expires_at              = (($exp = data_get($btInv,'expiration',null))!=null ? Carbon::createFromTimestamp($exp)->toDateTimeString() : null);
                $hbInv->created_at              = Carbon::createFromTimestamp(strtotime(data_get($btInv,'created','')))->toDateTimeString();

                $hbInv->save();
                
                if($btInv->remainingweight==0 && $hbInv->quantity_on_hand>0)
                    $invNopeHash[$hbInv->id] = $btInv->id;                      // we will adjust whatever reconcilles to 0, to match biotrack

                $invCount++;
                data_set($catTypeHash,data_get($prodHash,$btInv->productid,'XXX'),data_get($typeHash,$btInv->inventorytype ?: 'XXXXX',null),true);
                $invHash[$btInv->id] = $hbInv->id;
            }
$this->comment(' -- migrated chunk of 100 Inventory items - '.$invCount);
        });
        
        $jobline[] = '7/8 Successfully Migrated '.$invCount.' Inventory Items and their grade pricing sets';
$this->info('7/8. Successfully Migrated '.$invCount.' Inventory Items and their grade pricing sets');        

        /* 11 sync relational data */
        foreach($prodPriceSync as $prodId => $sync){
            if(($prod = Product::with('pricing','category')->find($prodId ?: 'XXXXX'))!=null){
                $prod->pricing()->sync(array_keys($sync));                      // sync pricing grades with the product
                
                if($prod->category){
                    $prod->category->pricing()->detach(array_keys($sync));
                    $prod->category->pricing()->attach(array_keys($sync));
                }
            }
        }
$this->comment(' -- Synced Product Grade Pricing');
        foreach($catTypeHash as $prodId => $type){
            if(($prod = Product::with('category')->find($prodId ?: 'XXXXX'))!=null){
                if($prod->category && $type){
                    $prod->category->public_img = data_get($type,'public_img',null);
                    $prod->category->equivalency_type = ($prod->category->equivalency_type=='noncannabis' ? data_get($type,'equivalency_type','noncannabis') : $prod->category->equivalency_type);
                    $prod->category->contains_thc = ($prod->category->equivalency_type=='noncannabis' ? false : true);
                    $prod->category->save();
                }
            }
        }
$this->comment(' -- Synced category type settings');

        $jobline[] = '11. Successfully Synced relational data - product preicests, category types, aggregates';
$this->info('11. Successfully Synced relational data - product preicests, category types, sale/customer reward aggregates'); 


        /* 9 INSERT ALL SALES SINCE BACKFILL OR product stock */
        $saleCount = 0;
        $this->service->from('sales')->select(
            'id','ticketid','strain','price','tax','total','pricepoint','productid','customerid','weight','requiresweighing','taxcat','datetime','inventoryid','ismedicated','deleted','refunded','terminalid','price_post_discount'
        )->where('location',$btLocId)->where('deleted', 0)->where('refunded',0)->where(function($q)use($backfill,$stockScope){
            $q->where('datetime', '>=', strtotime($backfill))->orWhereIn('inventoryid',$stockScope);
        })->orderBy('datetime','desc')->chunk(100,function($btSaleItems)use(&$saleHash,&$saleCount,$invHash,$custHash,$hbDraw,$defCust){
            foreach($btSaleItems as $btSaleItem){
                        
                        if(($hbInvId = data_get($invHash,$btSaleItem->inventoryid ?: 'XXXXX',null))==null) continue; // need inventory link!
                        
                        $hbSaleItem = new SaleItem;
                        $hbSaleItem->inventory_id = $hbInvId;
                        $hbSaleItem->quantity = (float)$btSaleItem->weight;
                        $hbSaleItem->price = (float)$btSaleItem->price;
                        $hbSaleItem->tax = ((float)$btSaleItem->price*(float)$btSaleItem->tax);
                        $hbSaleItem->sale_price = (float)$btSaleItem->total;
                        $hbSaleItem->discount = ($hbSaleItem->sale_price - $hbSaleItem->tax - $hbSaleItem->price); // the difference if discounted.
                        $hbSaleItem->thc_equivalent_grams = null;
                        
                        $hbSaleItem->migrated_at = Carbon::now()->toDateTimeString();
                        $hbSaleItem->created_at = Carbon::createFromTimestamp($btSaleItem->datetime)->toDateTimeString();

                        if(!isset($saleHash[$btSaleItem->ticketid]) || !isset($hbSale)){
                            $btSale = $this->service->from('tickets')->where('id',$btSaleItem->ticketid)->first();         
                            
                            $hbSale = Sale::find(data_get($saleHash,$btSaleItem->ticketid,'XXXXX')) ?: new Sale;
                            $hbSale->customer_id = data_get($custHash,$btSaleItem->customerid ?: 'XXXXX',$defCust->id);
                            $hbSale->user_id = $this->user->id;
                            $hbSale->drawer_id = $hbDraw[date('n',Carbon::createFromTimestamp(strtotime($btSale->created))->timestamp)]->id;
                            $hbSale->order_number = $btSale->transactionid;
                            $hbSale->price = (float)$btSale->price;
                            $hbSale->tax = ($hbSale->price*(float)$btSaleItem->tax);
                            $hbSale->sale_price = (float)$btSale->totalprice;
                            $hbSale->discount = ($hbSale->sale_price - $hbSale->tax - $hbSale->price); // the difference if discounted.
                            //$hbSale->thc_equivalent_grams = (28 - (float)preg_replace('/[^0-9]/s','',data_get($btSale,'remaining_limit',0)));
                            $hbSale->discount_code = null;
                            $hbSale->discount_descriptor = null;
                            $hbSale->status = 'settled';
                            $hbSale->migrated_at = Carbon::now()->toDateTimeString();
                            $hbSale->created_at = Carbon::createFromTimestamp(strtotime($btSale->created))->toDateTimeString();
                            
                            $hbSale->save();
                            if(!isset($saleHash[$btSaleItem->ticketid])) $saleCount++;
                            $saleHash[$btSaleItem->ticketid] = $hbSale->id;
                            
                            /* 10 Map used discounts of sales */
                            // TODO..
            
        
        
        
                            $hbDraw[date('n',Carbon::createFromTimestamp(strtotime($btSale->created))->timestamp)]->current_balance = ($hbDraw[date('n',Carbon::createFromTimestamp(strtotime($btSale->created))->timestamp)]->current_balance + $hbSale->sale_price);
                            
                        }
                        
                        $hbSaleItem->sale_id = $hbSale->id;
                        $hbSaleItem->save();
                        $hbSale->save();
                        
            }
$this->comment(' -- migrated chunk of 100 SaleItems - '.$saleCount.' sales recorded..');
        });

        $jobline[] = '9/10. Successfully Migrated '.$saleCount.' Sales and the SaleItems';
$this->info('9/10. Successfully Migrated '.$saleCount.' Sales and the SaleItems');  



        /* 12. Map initial adjustment of inventory biotrack says theres none remaining.. */
        foreach($invNopeHash as $invId => $btInvId){
            if(($inv = Inventory::find($invId))!=null){

                $inv->quantity_adjust = ($inv->quantity_on_hand*-1);
                $inv->quantity_on_hand = 0;
                $inv->archived_at = Carbon::now()->toDateTimeString();
                
                if($inv->quantity_adjust!=0){ 
                    $log = new InventoryLog;
                    $log->inventory_id = $inv->id;
                    $log->type = 'quantity';
                    $log->value = $inv->quantity_adjust;
                    $log->reason_code = 'na';
                    $log->notes = 'Migration Initial Adjustment to Match Biotrak on '.Carbon::now()->toDateTimeString();
                    $log->save();
                }
                
                $inv->save();
$this->comment(' -- Adjusted init inv - to match w biotrak having no remainingweight '.$inv->item_barcode.' '.$inv->item_strain.' - '.$inv->quantity_adjust);
            }
        }
        
        foreach(Inventory::where('quantity_on_hand','<',0)->get() as $negInv){  // adjust any negative inventory to 0 and report on it.
            $negInv->quantity_adjust = ($negInv->quantity_on_hand*-1);
            $negInv->quantity_on_hand = 0;
            //$inv->archived_at = Carbon::now()->toDateTimeString();

            $log = new InventoryLog;
            $log->inventory_id = $negInv->id;
            $log->type = 'quantity';
            $log->value = $negInv->quantity_adjust;
            $log->reason_code = 'na';
            $log->notes = 'Migration Adjustment of a Negative Qty to 0 - on '.Carbon::now()->toDateTimeString();
            $log->save();

            $negInv->save();
$this->comment(' -- Adjusted init inv - from a (-) count to 0 '.$negInv->item_barcode.' '.$negInv->item_strain.' - '.$negInv->quantity_adjust);
        }

        DB::table('dispensary_inventories')->where('location_id',$this->location->id)->where('quantity_on_hand','<=',0)->update(['archived_at' => Carbon::now()->toDateTimeString()]);


        for($i=1;$i<=12;$i++){
            $hbDraw[$i]->closing_balance = $hbDraw[$i]->current_balance;        // close the migration drawer
            $hbDraw[$i]->closed_at = Carbon::now()->toDateTimeString();
            $hbDraw[$i]->save();
        }
        
        
        /* 11.5 - RESAVE all product blueprints to get meta data ad nature_type */
        foreach(Product::get() as $prod)
            $prod->save();

        $jobline[] = '12. Successfully made initial inventory adjustmets and closed the migration articles';
$this->info('12. Successfully made initial inventory adjustmets and closed the migration articles'); 

        $jobline[] = 'Successfully Migrated '.$catCount.' Categories, '.$prodCount.' Products, '.$saleCount.' Sales Since '.$backfill->format('m/d/y');
$this->info('Successfully Migrated '.$catCount.' Categories, '.$prodCount.' Products, '.$saleCount.' Sales Since '.$backfill->format('m/d/y'));
        $this->location->status = 'activated';
        $this->location->save();
        
        Mail::to($this->user->email)
            ->queue(new UserMigrated($this->user));
        //return $jobline;
        
       /* ###################################### End BioTrakMigration Job ################################ */

    }
}
