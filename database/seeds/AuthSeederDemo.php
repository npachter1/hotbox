<?php


use App\Models\Auth\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



/**
 * Class UsersTableSeeder.
 */
class AuthSeederDemo extends Seeder
{

    const LOC_REC_ID = 12345;
    const LOC_REC2_ID = 99999;
    const LOC_GROW_ID = 67890;

    const USER_ADMIN_ID = 'ac9a7b98-48c2-4bbe-953e-08f557b128fb';
    const USER_SADMIN_ID = 'fa4cfb45-5b6e-4472-a679-8c654f2ed9c2';
    const USER_DADMIN_ID = 'ab5ac178-a3f5-4558-8ec4-48a9e41ff52d';
    const USER_DBUDTENDER_ID = '83d067db-f94d-455f-ae0e-a95d394a2c0d';
    const USER_SUPER_ID = '43039dd9-f89a-4c30-83c1-c1b8e4b90bf6';

    const ROLE_ADMINISTRATOR = 1;
    const ROLE_GROW_ADMINISTRATOR = 2;
    const ROLE_RETAIL_ADMINISTRATOR = 3;
    const ROLE_GROW_MANAGER = 4;
    const ROLE_RETAIL_MANAGER = 5;
    const ROLE_GROWER = 6;
    const ROLE_BUDTENDER = 7;
    const ROLE_SUPERADMIN = 8;

    const STORE_ADMIN_UPDATE = 1;
    const STAFF_UPDATE = 2;
    const REPORTING_VIEW = 3;
    const LOYALTY_UPDATE = 4;
    const PRODUCT_UPDATE = 5;
    const RECEIVING_UPDATE = 6;
    const MANAGE_LOCATION = 7;
    const MANAGE_SERVER = 8;
    
    const GROW_ROOMS_UPDATE = 9;
    const GROW_ROOMS_VIEW = 10;
    const GROW_STRAINS_UPDATE = 11;
    const GROW_STRAINS_VIEW = 12;
    const GROW_PACKAGES_UPDATE = 13;
    const GROW_PACKAGES_VIEW = 14;
    const GROW_PLANT_BATCHES_UPDATE = 15;
    const GROW_PLANT_BATCHES_VIEW = 16;
    const GROW_PLANTS_UPDATE = 17;
    const GROW_PLANTS_VIEW = 18;
    const GROW_HARVESTS_UPDATE = 19;
    const GROW_HARVESTS_VIEW = 20;
    const GROW_ITEMS_UPDATE = 21;
    const GROW_ITEMS_VIEW = 22;

    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        /* first 4 locations */
        DB::table('auth_locations')->insert([
            [
                'id'                => self::LOC_REC_ID,
                'name'              => 'Demo Rec Dispensary',
                'is_demo'           => 0,
                'status'            => 'activated',
                'type'              => 'dispensary',
                'licensenum'        => '402R-00132',                            // bgood case study TODO remove from seeder..
                'thumb'             => null,
                'settings'          => json_encode((object)[
                    'communication_email'   => 'support@hotboxerp.com',
                    'metrc_token'           => 'TXN4MzhCUFZHZWlLT2ZGQ0dMR3BiRDFRVGZiLUpRakRDaHZSd1hoN1Q1V09uenVoOjdmZkJuVmNhN0ppUDRGb1JuY2dkVTdwYk95YnpINEM0',
                    'metrc_uri'             => 'https://api-co.metrc.com',
                    'communication_timezone'=> 'America/Denver',
                    'campaign_sms_limit'    => 200,
                    'is_medical'            => false,
                    'regulatory_agent'      => 'metrc',
                    'grams_per_ounce'       => 28,
                    'sale_timeout_after_minutes' => 60,
                    'use_queue'             => true,
                    'use_confirm'           => false,
                    'tax_id'                => null
                ]),
                'migration_settings'=> null,
                'activated_at'      => '2019-01-01 00:00:01',
                'created_at'        => '2019-01-01 00:00:01'
            ],
            [
                'id'                => self::LOC_REC2_ID,
                'name'              => 'Demo Med Dispensary',
                'is_demo'           => 1,
                'status'            => 'activated',
                'type'              => 'dispensary',
                'licensenum'        => '402-00670',                            // bgood case study TODO remove from seeder..
                'thumb'             => null,
                'settings'          => json_encode((object)[
                    'communication_email'   => 'support@hotboxerp.com',
                    'communication_timezone'=> 'America/Denver',
                    'campaign_sms_limit'    => 200,
                    'metrc_token'           => 'TXN4MzhCUFZHZWlLT2ZGQ0dMR3BiRDFRVGZiLUpRakRDaHZSd1hoN1Q1V09uenVoOjdmZkJuVmNhN0ppUDRGb1JuY2dkVTdwYk95YnpINEM0',
                    'metrc_uri'             => 'https://api-co.metrc.com',
                    'is_medical'            => true,
                    'regulatory_agent'      => 'metrc',
                    'grams_per_ounce'       => 28,
                    'sale_timeout_after_minutes' => 60,
                    'use_queue'             => true,
                    'use_confirm'           => false,
                    'tax_id'                => null
                ]),
                'migration_settings'=> json_encode((object)[
                    'btdb_host'             => '96.93.210.70',
                    'btdb_port'             => '5432',
                    'btdb_dbname'           => 'biotrackthc',
                    'btdb_schema'           => 'public',
                    'btdb_username'         => 'hotbox',
                    'btdb_password'         => 'hotbox123',
                    'btdb_location_id'     => 3,
                    'backfill'              => '2019-01-01 00:00:01'
                ]),
                'activated_at'      => '2019-01-01 00:00:01',
                'created_at'        => '2019-01-01 00:00:01'
            ],
            [
                'id'                => self::LOC_GROW_ID,
                'name'              => 'Demo Grow (CO)',
                'is_demo'           => 1,
                'status'            => 'activated',
                'type'              => 'grow',
                'licensenum'        => '403-X0001', // CO sandbox
                'thumb'             => null,
                'settings'          => json_encode((object)[
                    'communication_email'   => 'support@hotboxerp.com',
                    'communication_timezone'=> 'America/Denver',
                    'campaign_sms_limit'    => 200,
                    'metrc_token'           => 'QUo5TU9RLTR4QWlSNlV5a2hyTENFVk5pS1FNOXFBd2VTRGI0LXhxMFI5RHhlNFItOkVCUk9keS1ORFVGaGhMNk04eFF2OXk0Z3hjNlZrR2tpT2xCU2pVRzhZQnZyNnNzbQ==',
                    'metrc_uri'             => 'https://sandbox-api-co.metrc.com',
                    'is_medical'            => true,
                    'regulatory_agent'      => 'metrc',
                    'grams_per_ounce'       => 28,
                    'sale_timeout_after_minutes' => 60,
                    'use_queue'             => true,
                    'use_confirm'           => false,
                    'tax_id'                => null
                ]),
                'migration_settings'=> null,
                'activated_at'      => '2019-01-01 00:00:01',
                'created_at'        => '2019-01-01 00:00:01'
            ]
        ]);


        /* first 12 users */
        DB::table('users')->insert(
            [
                [ 'id' => self::USER_ADMIN_ID, 'name' => 'Administrator', 'email' => 'admin@hotboxerp.com','type' => 'admin', 'location_id' => self::LOC_GROW_ID, 'password' => bcrypt('adminHotBox2019'), 'pincode' => '0000','status' => 'activated', 'created_at'=>'2018-01-01 00:00:01'],
                [ 'id' => self::USER_SADMIN_ID, 'name' => 'Sandbox Admin', 'email' => 'sadmin@hotboxerp.com','type' => 'admin', 'location_id' => self::LOC_GROW_ID, 'password' => bcrypt('sadminHotBox2019'), 'pincode' => '1111', 'status' => 'activated','created_at'=>'2018-01-01 00:00:01'],
                [ 'id' => self::USER_DADMIN_ID, 'name' => 'Dispensary Admin', 'email' => 'dadmin@hotboxerp.com','type' => 'admin', 'location_id' => self::LOC_REC_ID, 'password' => bcrypt('dadminHotBox2019'), 'pincode' => '5555', 'status' => 'activated', 'created_at'=>'2018-01-01 00:00:01'],
                [ 'id' => self::USER_DBUDTENDER_ID, 'name' => 'Dispensary Budtender', 'email' => 'dbudtender@hotboxerp.com','type' => 'staff', 'location_id' => self::LOC_REC_ID, 'password' => bcrypt('dbudtenderHotBox2019'), 'pincode' => '7777', 'status' => 'activated', 'created_at'=>'2018-01-01 00:00:01'],
                [ 'id' => self::USER_SUPER_ID, 'name' => 'Super Admin', 'email' => 'superadmin@hotboxerp.com','type' => 'contractor', 'location_id' => self::LOC_REC_ID, 'password' => bcrypt('SuperHotBox2019'), 'pincode' => '9876', 'status' => 'activated', 'created_at'=>'2018-01-01 00:00:01']
            ]
        );

        DB::table('auth_permissions')->insert(
            [
                [ 'id' => self::STAFF_UPDATE, 'name' => 'Staff Update', 'guard_name' => 'api' ],
                [ 'id' => self::STORE_ADMIN_UPDATE, 'name' => 'Store Admin Update', 'guard_name' => 'api' ],                
                [ 'id' => self::REPORTING_VIEW, 'name' => 'Reporting View', 'guard_name' => 'api' ],
                [ 'id' => self::LOYALTY_UPDATE, 'name' => 'Loyalty Update', 'guard_name' => 'api' ],
                [ 'id' => self::PRODUCT_UPDATE, 'name' => 'Product Update', 'guard_name' => 'api' ],
                [ 'id' => self::RECEIVING_UPDATE, 'name' => 'Receiving Update', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_ROOMS_UPDATE, 'name' => 'Grow Rooms Update', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_ROOMS_VIEW, 'name' => 'Grow Rooms View', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_STRAINS_UPDATE, 'name' => 'Grow Strains Update', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_STRAINS_VIEW, 'name' => 'Grow Strains View', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_PACKAGES_UPDATE, 'name' => 'Grow Packages Update', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_PACKAGES_VIEW, 'name' => 'Grow Packages View', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_PLANT_BATCHES_UPDATE, 'name' => 'Grow Plant Batches Update', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_PLANT_BATCHES_VIEW, 'name' => 'Grow Plant Batches View', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_PLANTS_UPDATE, 'name' => 'Grow Plants Update', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_PLANTS_VIEW, 'name' => 'Grow Plants View', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_HARVESTS_UPDATE, 'name' => 'Grow Harvests Update', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_HARVESTS_VIEW, 'name' => 'Grow Harvests View', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_ITEMS_UPDATE, 'name' => 'Grow Items Update', 'guard_name' => 'api' ],
                [ 'id' => self::GROW_ITEMS_VIEW, 'name' => 'Grow Items View', 'guard_name' => 'api' ],
                [ 'id' => self::MANAGE_LOCATION, 'name' => 'Manage Location', 'guard_name' => 'api' ],
                [ 'id' => self::MANAGE_SERVER, 'name' => 'Manage Server', 'guard_name' => 'api' ],
            ]
        );


        DB::table('auth_roles')->insert(
            [
                [ 'id' => self::ROLE_ADMINISTRATOR, 'name' => 'Administrator', 'guard_name' => 'api' ],
                [ 'id' => self::ROLE_GROW_ADMINISTRATOR, 'name' => 'Grow Administrator', 'guard_name' => 'api' ],
                [ 'id' => self::ROLE_RETAIL_ADMINISTRATOR, 'name' => 'Retail Administrator', 'guard_name' => 'api' ],
                [ 'id' => self::ROLE_GROW_MANAGER, 'name' => 'Grow Manager', 'guard_name' => 'api' ],
                [ 'id' => self::ROLE_RETAIL_MANAGER, 'name' => 'Retail Manager', 'guard_name' => 'api' ],
                [ 'id' => self::ROLE_GROWER, 'name' => 'Grower', 'guard_name' => 'api' ],
                [ 'id' => self::ROLE_BUDTENDER, 'name' => 'Budtender', 'guard_name' => 'api' ],
                [ 'id' => self::ROLE_SUPERADMIN, 'name' => 'super-admin', 'guard_name' => 'api' ]
            ]
        );

        DB::table('auth_role_has_permissions')->insert(
            [

                // ADMINISTRATOR
                [ 'permission_id' => self::STAFF_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::STORE_ADMIN_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::REPORTING_VIEW, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::LOYALTY_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::PRODUCT_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::RECEIVING_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_ROOMS_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_ROOMS_VIEW, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_STRAINS_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_STRAINS_VIEW, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PACKAGES_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PACKAGES_VIEW, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PLANT_BATCHES_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PLANT_BATCHES_VIEW, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PLANTS_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PLANTS_VIEW, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_HARVESTS_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_HARVESTS_VIEW, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_ITEMS_UPDATE, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_ITEMS_VIEW, 'role_id' => self::ROLE_ADMINISTRATOR ],
                [ 'permission_id' => self::MANAGE_LOCATION, 'role_id' => self::ROLE_ADMINISTRATOR ],

                // GROW ADMINISTRATOR
                [ 'permission_id' => self::STAFF_UPDATE, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::REPORTING_VIEW, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_ROOMS_UPDATE, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_ROOMS_VIEW, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_STRAINS_UPDATE, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_STRAINS_VIEW, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PACKAGES_UPDATE, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PACKAGES_VIEW, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PLANT_BATCHES_UPDATE, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PLANT_BATCHES_VIEW, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PLANTS_UPDATE, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_PLANTS_VIEW, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_HARVESTS_UPDATE, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_HARVESTS_VIEW, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_ITEMS_UPDATE, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::GROW_ITEMS_VIEW, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                [ 'permission_id' => self::MANAGE_LOCATION, 'role_id' => self::ROLE_GROW_ADMINISTRATOR ],
                
                // RETAIL ADMINISTRATOR
                [ 'permission_id' => self::STAFF_UPDATE, 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR ],
                [ 'permission_id' => self::STORE_ADMIN_UPDATE, 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR ],
                [ 'permission_id' => self::REPORTING_VIEW, 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR ],
                [ 'permission_id' => self::LOYALTY_UPDATE, 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR ],
                [ 'permission_id' => self::PRODUCT_UPDATE, 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR ],
                [ 'permission_id' => self::RECEIVING_UPDATE, 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR ],
                [ 'permission_id' => self::MANAGE_LOCATION, 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR ],
                
                // GROW MANAGER
                [ 'permission_id' => self::STAFF_UPDATE, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::REPORTING_VIEW, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_ROOMS_UPDATE, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_ROOMS_VIEW, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_STRAINS_UPDATE, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_STRAINS_VIEW, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_PACKAGES_UPDATE, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_PACKAGES_VIEW, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_PLANT_BATCHES_UPDATE, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_PLANT_BATCHES_VIEW, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_PLANTS_UPDATE, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_PLANTS_VIEW, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_HARVESTS_UPDATE, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_HARVESTS_VIEW, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_ITEMS_UPDATE, 'role_id' => self::ROLE_GROW_MANAGER ],
                [ 'permission_id' => self::GROW_ITEMS_VIEW, 'role_id' => self::ROLE_GROW_MANAGER ],
                                                       
                // RETAIL MANAGER
                [ 'permission_id' => self::STAFF_UPDATE, 'role_id' => self::ROLE_RETAIL_MANAGER ],
                [ 'permission_id' => self::STORE_ADMIN_UPDATE, 'role_id' => self::ROLE_RETAIL_MANAGER ],
                [ 'permission_id' => self::REPORTING_VIEW, 'role_id' => self::ROLE_RETAIL_MANAGER ],
                [ 'permission_id' => self::LOYALTY_UPDATE, 'role_id' => self::ROLE_RETAIL_MANAGER ],
                [ 'permission_id' => self::PRODUCT_UPDATE, 'role_id' => self::ROLE_RETAIL_MANAGER ],
                [ 'permission_id' => self::RECEIVING_UPDATE, 'role_id' => self::ROLE_RETAIL_MANAGER ],
                                                       
                // GROWER
                [ 'permission_id' => self::REPORTING_VIEW, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_ROOMS_UPDATE, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_ROOMS_VIEW, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_STRAINS_UPDATE, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_STRAINS_VIEW, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_PACKAGES_UPDATE, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_PACKAGES_VIEW, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_PLANT_BATCHES_UPDATE, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_PLANT_BATCHES_VIEW, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_PLANTS_UPDATE, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_PLANTS_VIEW, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_HARVESTS_UPDATE, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_HARVESTS_VIEW, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_ITEMS_UPDATE, 'role_id' => self::ROLE_GROWER ],
                [ 'permission_id' => self::GROW_ITEMS_VIEW, 'role_id' => self::ROLE_GROWER ],

                                                       
                // BUDTENDER
                [ 'permission_id' => self::REPORTING_VIEW, 'role_id' => self::ROLE_BUDTENDER ]
            ]
        );

        DB::table('auth_model_has_roles')->insert(
            [
                [ 'role_id' => self::ROLE_ADMINISTRATOR, 'model_uuid' => self::USER_ADMIN_ID, 'model_type' => 'App\Models\Auth\User' ],
                [ 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR, 'model_uuid' => self::USER_ADMIN_ID, 'model_type' => 'App\Models\Auth\User' ],
                [ 'role_id' => self::ROLE_RETAIL_MANAGER, 'model_uuid' => self::USER_ADMIN_ID, 'model_type' => 'App\Models\Auth\User' ],
                
                [ 'role_id' => self::ROLE_ADMINISTRATOR, 'model_uuid' => self::USER_SADMIN_ID, 'model_type' => 'App\Models\Auth\User' ],
                [ 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR, 'model_uuid' => self::USER_SADMIN_ID, 'model_type' => 'App\Models\Auth\User' ],
                [ 'role_id' => self::ROLE_RETAIL_MANAGER, 'model_uuid' => self::USER_SADMIN_ID, 'model_type' => 'App\Models\Auth\User' ],

                [ 'role_id' => self::ROLE_RETAIL_ADMINISTRATOR, 'model_uuid' => self::USER_DADMIN_ID, 'model_type' => 'App\Models\Auth\User' ],
                [ 'role_id' => self::ROLE_RETAIL_MANAGER, 'model_uuid' => self::USER_DADMIN_ID, 'model_type' => 'App\Models\Auth\User' ],

                [ 'role_id' => self::ROLE_BUDTENDER, 'model_uuid' => self::USER_DBUDTENDER_ID, 'model_type' => 'App\Models\Auth\User' ],
                [ 'role_id' => self::ROLE_SUPERADMIN, 'model_uuid' => self::USER_SUPER_ID, 'model_type' => 'App\Models\Auth\User' ]
            ]
        );
        
        DB::table('auth_location_user')->insert(
            [
                [ 'user_id' => self::USER_SUPER_ID, 'auth_location_id' => self::LOC_REC_ID],
                [ 'user_id' => self::USER_SUPER_ID, 'auth_location_id' => self::LOC_REC2_ID],
                [ 'user_id' => self::USER_SUPER_ID, 'auth_location_id' => self::LOC_GROW_ID],

                [ 'user_id' => self::USER_ADMIN_ID, 'auth_location_id' => self::LOC_GROW_ID],
                [ 'user_id' => self::USER_ADMIN_ID, 'auth_location_id' => self::LOC_REC_ID],
                [ 'user_id' => self::USER_ADMIN_ID, 'auth_location_id' => self::LOC_REC2_ID],

                [ 'user_id' => self::USER_SADMIN_ID, 'auth_location_id' => self::LOC_GROW_ID], // grow sandbox staff
                [ 'user_id' => self::USER_DADMIN_ID, 'auth_location_id' => self::LOC_REC_ID], // d admin has 3 locations and then dispensary staff to the first
                [ 'user_id' => self::USER_DADMIN_ID, 'auth_location_id' => self::LOC_REC2_ID],
                [ 'user_id' => self::USER_DBUDTENDER_ID, 'auth_location_id' => self::LOC_REC_ID]
            ]
        );


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
    
    
}
