<?php

namespace App\Services\Auth;

use App\Mail\UserReactivation;
use App\Services\BaseService;
use App\Mail\UserActivation;
use App\Mail\UserActivated;
use App\Mail\PasswordReset;
use App\Mail\PasswordResetted;
use App\Mail\UserRegistered;
use App\Mail\LocationRequestActivation;

use App\Http\Resources\Auth\UserCollectionExport;

use App\Models\AppSchema;
use App\Models\Auth\User;
use App\Models\Auth\Location;
use App\Models\Auth\Addressbook;

use App\Models\Dispensary\Tax;
use App\Models\Dispensary\TaxRate;
use App\Models\Dispensary\Category;

use App\Helpers\Generator;
use App\Helpers\Util;

use Auth;
use Exception;
use stdClass;
use Carbon\Carbon;
use DB;
use PDF;
use Mail;
use Gravatar;
use Notification;
use App\Notifications\UserNotification;


class AuthService extends BaseService
{
    protected static $PAGINATION_SIZE = 50;
    protected static $MAX_PAGINATION_SIZE = 200;


    public function searchUsers(array $data){
        
        $page = (int)array_get($data, 'page', null);
        $limit = (int)array_get($data, 'limit', self::$PAGINATION_SIZE);
        $sort = array_get($data, 'sort', null);

        $search = array_get($data, 'search', null);
        $filters = array_get($data,'filter',[]);
        $range = [data_get($data, 'from', null),data_get($data, 'to', null)];

        $query = User::query()
            ->with('roles')
            ->select('id','location_id','name','email','type','provider','status','avatar','settings','created_at','updated_at')
            ->ofListFilters($filters)
            ->ofTextFilter($search);
            
        if(data_get($data,'archived',0)==0) $query->whereNotIn('status',['archived'])->ofActive(); // unless we are searhing w archived toggle on, then only get active.

        if($sort) $query->ofOrderByFilter($sort);
        else $query->orderBy('created_at', 'desc');


        return $query->paginate($limit);
        
    }
    
    
    /* get a user record */
    public function showUser(array $data,$id){
        
        return User::query()
            ->where('id',$id)
            ->select('id','location_id','name','email','provider','status','avatar','settings','created_at','updated_at')
            ->with('roles')
            ->first();
            
    }

    public function showLocation(array $data,$id){
        
        return Location::query()
            ->where('id',$id)
            ->first();
            
    }
    
    
    /* store new user (internally) */
    public function storeUser($data){
        
        if(($cur = Auth::guard('api')->user()) == null) abort(401,'Whoops, no user logged in - please reauthenticate');


        $user = new User();
        $user->location_id = $cur->location_id;                                       // assigned to current logged in users location
        $user->email = data_get($data, 'email', null);
        $user->status = data_get($data, 'status', 'pending_activation');
        $user->password = bcrypt(data_get($data, 'password', null));
        $user->name = data_get($data, 'name', null);
        
        if($user->status=='pending_activation') $user->activation_token = Generator::uuid4();
        
        
        $user->save();
        $user->locations()->sync([$user->location_id]);                         // sync logged in location to user  
        $user->roles()->sync(data_get($data,'role_ids',[]));                    // sync roles asigned.
        
        if($user->status=='pending_activation')
            Mail::to($user->email)->queue(new UserActivation($user));           // queue an activation link email to new user if not activated upon creation  
        
        
        return $user;
        
    }


    /* update user profile */
    public function updateUserProfile($data,$id){
        
        if(($cur = Auth::guard('api')->user()) == null) abort(401,'Whoops, no user logged in - please reauthenticate');
        elseif(($user = User::find($id)) == null) abort(400,'Whoops, User  Data is temporarially disonnected - please try again later');

        $user->fill($data);
        
        $user->archived_at = data_get($data,'archived_at',$user->archived_at);
        $user->save();

        $user->roles()->sync(data_get($data,'role_ids',[]));                    // sync roles asigned.


        return $user;
        
    }
    
    
    /* send a (new) activation request for a user */
    public function sendActivationRequest($user,$data){
        
        $user->activation_token = Generator::uuid4();
        $user->save();
        
        
        Mail::to($user->email)
            ->queue(new UserActivation($user));       
        //$user->notify(new Activation($user)); //HINT: using a mail class vs notification for more robust control.
        
        
        return $user;
        
    }


    /* activate user */
    public function activateUser(User $user){
        
        $user->status = 'activated';
        $user->archived_at = null;
        $user->save();
        
        Mail::to($user->email)
            ->queue(new UserActivated($user));     
        
        
        return $user;
        
    }
    
    
    public function archiveUser($data,$id,$user=null){
        
        if(($record = User::find($id)) == null) abort(409,'Cannot Archive - No Record found');

        // Any conditions to not archive, speak now or forever hold youre peace..
        

        $record->status = 'archived';
        $record->archived_at = Carbon::now()->toDateTimeString();
        $record->save();       
        //$record->delete();                                                    

        return true;
        
    }
    
    /* unarchive the user */
    public function unarchiveUser($data,$id,$user=null){
        
        if(($record = User::find($id)) == null) abort(409,'Cannot Un-Archive - No Record found');
        elseif($record->archived_at == null) abort(409,'User is not Archived');

        // Any conditions to not archive, speak now or forever hold youre peace..
        

        $record->status = 'activated';                                          // this will reactivate user
        $record->archived_at = null;
        $record->save();       
        //$record->delete();                                                    

        return [
            'status'    => 200,
            'message'   => 'User has been UnArchived',
            'schema'    => User::_getSchema()                                // include schema as these batch changes may update the scope of some filters.
        ];
        
    }
    
    
    
    /* initiate a reset password registration */
    public function initiatePasswordReset(User $user, $data){

        if(!$data) return false;
        
        $token = Generator::uuid4();


        DB::table('auth_password_resets')->insert([
            'email' => data_get($data, 'email', null),
            'token' => $token,
            'created_at' => Carbon::now()->toDateTimeString()
        ]);
        
        Mail::to($user->email)
            ->queue(new PasswordReset($user,$token)); 
            
            
        return $user;
        
    }

    /* email to admin asking them to activate this user */
    public function initiateReactivateUserRequest(User $user, $data){
        if(!$data) return false;

        Mail::to($user->location->settings->communication_email)
            ->queue(new UserReactivation($user));

        return $user;
    }

    /* reset a password */
    public function passwordReset(User $user, $data){
        
        if(!$data) return false;
        
        $user->password = bcrypt(data_get($data, 'password', null));
        $user->save();
                                        
        Mail::to($user->email)
            ->queue(new PasswordResetted($user));


        return $user;
        
    }
    
    
    /* update location & settings profile */
    public function updateLocationProfile($data,$id){
        
        if(($user = Auth::guard('api')->user()) == null) abort(401,'Whoops, no user logged in - please reauthenticate');
        elseif(($location = Location::find($id)) == null) abort(400,'Whoops, Location Data is temporarially disonnected - please try again later');

        $location->fill($data);
        
        if($user->can('Manage Server')) $location->status = data_get($data,'status',$location->status);
        $location->save();


        return $location;
        
    }
    

    /* process an export */
    public function exportCollection($data,$typ,$file){
        
        /* describe any filters and the date in the name */
        $name = date('mdyhi',time()).'_Users_';
        foreach(data_get($data,'filter',[]) as $key => $val)
            foreach(explode(',',$val) as $ind => $item)
                if($item!='all') 
                    $name .= ($ind==1 ? '-' : '').preg_replace('/[^a-zA-Z0-9]/','-',$item);
        $name .= '.'.$typ;


        return $this->export(new UserCollectionExport($data),$file,$name,$typ,[]);

    }

    
    /* process a single pdf export */
    public function exportPdf($data,$id,$file){
        
        if(!$id) throw new Exception('Whoops - Incomplete information sent - aborting.');
        
        $name = Carbon::now()->timestamp.'_profile.pdf';
        //data_set($data,'foo','bar',true);
        
        $pdf = PDF::loadView('print.test', $data);
        //$pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        // https://github.com/barryvdh/laravel-dompdf
        
        
        return $this->export($pdf->output(),$file,$name,'pdf',[]);

    }


    
    /**                                     **/
	/**    KEY APPSUITE REGISTRATION        **/
	/**            UTILITIES                **/
    

    /* loda location data */
    public function loadLocationData($filter){
        
        if(($user = Auth::guard('api')->user()) == null) abort(400,'no user logged in - please reauthenticate');
        elseif(($location = $user->location) == null) abort(400,'User is not assigned to an active location - please reautheticate');
        
        $pay = new stdClass;
        $_appSchema = AppSchema::getSchema('app_registrar') ?: [];
        $_scope = explode(',',data_get($filter,'scope','all'));

        
        /* parse key user/location data for frontend store */
        if(in_array('all',$_scope) || in_array('user',$_scope))                 // user data to frontend
            $pay->user = (object)[
                    'avatar'            => $user->avatar,
                    'created_at'        => $user->created_at,
                    'email'             => $user->email,
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'settings'          => (object)$user->settings ?: new stdClass,
                    'status'            => $user->status,
                    'location'             => (object)[
                        'id'                => $location->id,
                        'addressbook_id'    => $location->addressbook_id,
                        'type'              => $location->type,
                        'licensenum'        => $location->licensenum,
                        'name'              => $location->name,
                        'thumb'             => $location->thumb,
                        'is_demo'           => (boolean) $location->is_demo,
                        'status'            => $location->status,
                        'address'           => $location->address,
                        'regulatory_agent'  => data_get($location->settings,'regulatory_agent','none'),
                        'activated_at'      => $location->activated_at,
                        'created_at'        => $location->created_at,
                        'updated_at'        => $location->updated_at
                    ],
                    'locations_assigned'=> $user->locationsAssigned,
                    'roles'             => $user->roles()->pluck('name')->toArray(),
                    'permissions'       => $user->getAllPermissions()->pluck('name')->toArray()
                ];
            
        
        /* parse key app sections for location sub menu */
        if(in_array('all',$_scope) || in_array('sections',$_scope)){            // App Sections (curated by current location) data to frontend
            
            $pay->sections = [(object)[                                         // initial section to link back to dashboard
                'module'    => 'dashboard',
                'name'      => 'Home',
                'hidden'    => true,
                'views' => []
            ]];
            
            foreach((array)$_appSchema as $key => $sections)
                foreach($sections as $section)
                    if($user->can(data_get($section,'can',null)) && in_array($key,[strtolower($location->type),'auth']))
                        $pay->sections[] = $section;                            // add each apps sections to the sections array

        }

        /* add any needed messaging to app location top right bell */
        if(in_array('all',$_scope) || in_array('message',$_scope)){
            
            $pay->messages = [(object)[ // HINT app messages load here..
                'message'       => $user->name.', Welcome to '.config('app.name').'!',
                'alert'         => false,
                'created'       => Carbon::now()->toDateTimeString(),
                'route'         => null
            ]];

            if(getenv('PUSHER_APP_KEY')){
                
                //Send a welcome message only 1 time to the user
                if($user->notifications->count() === 0){
                    $details = [
                        'message'       => $user->name.', Welcome to '.config('app.name').'!',
                        'title' => 'Hello from '.config('app.name').'!'
                    ];
                    
                    $user->notify(new UserNotification($details));
                }

                // if we dont have metrc creds for a metrc regulatory type of location - persistantly warn
                if(($metrc = data_get($user->location,'settings.regulatory_agent','none'))=='metrc'){
                    if(($metrcCheck = data_get($user->location,'settings.metrc_token',null))==null){
                        $pay->messages = [(object)[
                            'message'       => 'URGENT - We do not have your Metrc Credentials for '.$user->location->name.' Please add..',
                            'alert'         => true,
                            'created'       => Carbon::now()->toDateTimeString(),
                            'route'         => 'location'
                        ]]; 

                        $details = [
                            'message'       => 'URGENT - We do not have your Metrc Credentials for '.$user->location->name.' Please add..',
                            'title'         => 'Urgent message from '.config('app.name').'!'
                        ];

                        $user->notify(new UserNotification($details));          // This is important so send always
                    }
                }
            }
            
        }
        
        $pay->settings = AppSchema::getSchema('services_registrar');
        return $pay;
        
    }
    
    /* toggle location mode */
    public function toggleMode($data,$user){
        
        $mode = (data_get($user->location,'is_demo',false)==true ? false : true);
        $user->location->is_demo = $mode;
        $user->location->save();
        
        
        return true;
        
    }

    /* update label printer default settings */
    public function updateLabelPrinter($data,$id,$user){
        
        if(($location = Location::find($id)) == null) abort(400,'Whoops, Location Data is temporarially disonnected - please try again later');
               
        $printer[] = (object) array_get($data,'label_printer',null);

        $label_printers = isset($location->settings->label_printers) ? (array) $location->settings->label_printers : [] ;

        $label_printers = array_map(function($a) use($printer){
                if($a->type === $printer[0]->type && $a->route === $printer[0]->route){
                    return $printer[0];
                }
                if($a->route === $printer[0]->route && $printer[0]->isDefault)
                {
                    $a->isDefault = false;
                }
            return $a;
        }, $label_printers);

        if(count($label_printers) > 0){
            $label_printers = array_merge($label_printers, $printer);
            $label_printers = array_unique($label_printers, SORT_REGULAR);
        }
        else
        {
            $label_printers = $printer;
        }

        $location->_setSetting('label_printers', $label_printers);

        $location->save();

        return $location;
    
    }
    
    /* validate an admin pin */
    public function validatePin($data,$caller){
        
        //if(($admin = User::where('status','activated')->where('location_id',data_get($data,'location_id',null))->where('pincode',data_get($data,'pin',null))->first())==null) return false;
        if(($admin = User::where('status','activated')->where('pincode',data_get($data,'pin',null))->first())==null) return false;
        elseif(!$admin->can('Store Admin Update')) return false;
        else return true;

    }
    
    
    /* register a new Location! */
    public function registerNewLocation($data,$user){
        
        if(!$data || !$user) return false;
        
        /* 1. create new location without activated date */
        $location = new Location;
        
        $location->name = data_get($data,'name', null);
        $location->type = data_get($data,'type','dispensary');
        $location->licensenum = data_get($data,'licensenum',null);
        $location->status = 'activated';
        $location->is_demo = true;
        $location->settings = [
                'communication_email'   => data_get($data, 'email', null),
                'communication_sms'     => null,
                'metrc_token'           => null,
                'metrc_uri'             => 'https://api-co.metrc.com',
                'communication_timezone'=> data_get($user->location,'settings.communication_timezone','America/Denver'),
                'campaign_sms_limit'    => 200,
                'is_medical'            => data_get($data,'is_medical',false),
                'regulatory_agent'      => data_get($user->location,'settings.regulatory_agent','metrc'),
                'grams_per_ounce'       => data_get($user->location,'settings.grams_per_ounce',28),
                'sale_timeout_after_minutes' => data_get($user->location,'settings.sale_timeout_after_minutes',60),
                'use_queue'             => data_get($user->location,'settings.use_queue',true),
                'use_confirm'           => data_get($user->location,'settings.use_confirm',false),
                'tax_id'                => null
        ];
            
        $location->save();
        $user->location_id = $location->id;
        $user->save();
        
        
        Auth::guard('api')->setUser($user); // for scoping of new data..
        
        $adr = Addressbook::create([
            'location_id'       => $location->id,
            'third_party_id'    => null,
            'type'              => 'location',
            'name'              => $location->name,
            'address1'          => data_get($data,'address.address1',null),
            'address2'          => null,
            'city'              => data_get($data,'address.city',null),
            'region'            => data_get($data,'address.region',null),
            'country'           => 'US',
            'county'            => data_get($data,'address.county',null),
            'zip'               => data_get($data,'address.zip',null),
            'phone'             => null,
            'contact_notes'     => null,
            'email'             => null,
            'licensenum'        => $location->licensenum,
            'website'           => null,
            'industry'          => $location->type
        ]); 
        $location->addressbook_id = $adr->id;
        $location->save();


        /* 2. Sync roles and location to users, and location to super user */
        $user->locations()->attach([$location->id]);                            // attach new location to location id

        if(($super = User::where('email','like','%superadmin%')->first()) != null)
            if($super->id!=$user->id)
                $super->locations()->attach($location->id);                     // sync new location to super admin user


        /* 3 generate initial data (noncannabis, medical, recreational) dispensary_taxes, TODO IN V2: sync w appropiate tax rates for this new location */
        $rate = new TaxRate;
        $rate->name = 'Default State Tax';
        $rate->description = 'Default tax rate';
        $rate->tax_rate_level = 'state';
        $rate->state_code = data_get($data,'address.region',null);
        $rate->county_name = data_get($data,'address.county',null);
        $rate->city_name = data_get($data,'address.city',null);
        $rate->zipcode = data_get($data,'address.zip',null);
        $rate->rate_percent = 2.9;
        $rate->save();
        
        $tax = new Tax;
        $tax->location_id = $location->id;
        $tax->nature_type = (data_get($data,'is_medical',false)===true ? 'medical' : 'recreational');
        $tax->save();

        $tax->rates()->sync([$rate->id]);
        
        
        /* initial categories */
        $defCat = new Category;
        $defCat->name = 'Flower/Buds';
        $defCat->contains_thc = true;
        $defCat->equivalency_type = 'flower';
        $defCat->public_img = Util::guessImagery('bud');
        $defCat->save();
        
        $defCat = new Category;
        $defCat->name = 'Shake/Trim';
        $defCat->contains_thc = true;
        $defCat->equivalency_type = 'shake/trim';
        $defCat->public_img = Util::guessImagery('shake');
        $defCat->save();

        $defCat = new Category;
        $defCat->name = 'Edibles';
        $defCat->contains_thc = true;
        $defCat->equivalency_type = 'edible';
        $defCat->public_img = Util::guessImagery('edible');
        $defCat->save();        
        
        $defCat = new Category;
        $defCat->name = 'Concentrates';
        $defCat->contains_thc = true;
        $defCat->equivalency_type = 'concentrate';
        $defCat->public_img = Util::guessImagery('concentrate');
        $defCat->save();
        
        $defCat = new Category;
        $defCat->name = 'Non-Cannabis';
        $defCat->contains_thc = false;
        $defCat->equivalency_type = 'noncannabis';
        $defCat->public_img = null;
        $defCat->save();


        return $location;

    }
    
    
    
    /* register a new user from login (for future reference if desired) */
    public function createFromRegistration($data){
        
        if(!$data) return false;
        
        
        /* 1. create new location without activated date */
        $location = Location::create();
        $location->name = data_get($data,'company_name', null);
        $location->type = data_get($data,'type','dispensary');
        $location->status = 'registered';
        $location->settings = [
                'communication_email'   => data_get($data, 'email', null),
                'communication_sms'     => null
            ];


        /* 2. create Owner user and 1 employee user */
        $user = new User();
        $user->location_id = $location->id;                                           // assigned to current logged in users location
        $user->email = data_get($data, 'email', null);
        $user->status = 'activated';                                            // regristrant will be activated and authenticated vs pending_activation w email (if/when needed)
        $user->password = bcrypt(data_get($data, 'password', null));
        $user->name = data_get($data, 'name', null);
        $user->activation_token = Generator::uuid4();
        
        $user->avatar = Gravatar::fallback('/images/users/avatar.png')->get($user->email);  // update gravatar avatar
        $user->save();

        $location->save();
        
        
        /* 3. Sync roles and location to users, and location to super user */
        $user->locations()->sync([$location->id]);                              // sync location to user
        $user->syncRoles(['Administrator']);                                    // sync owner group to user
        
        if(($super = User::where('email','like','%superadmin%')->first()) != null)
            $super->locations()->attach($location->id);                         // sync new location to super admin user


        /* 4. queue email confirmation, authenticate and return */
        Mail::to($user->email)
            ->queue(new UserRegistered($user,$location)); 
        
        
        return $user;

    }


}