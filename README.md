## Hotbox Cloud V1
Server: Ubuntu 18 & PHP7.2/nginx/fpm
Framework: Laravel 5.8
Database: Mysql 5.6
Cache/Queue: Redis
Frontend: Vue2,webpack w npm 8,Bootstrap4

## Spin up
 - 1. checkout the develop branch of this repo and set up your env file (reference .env.example)
 - 2. run ./devReset.sh (or run the commands in that file)
 - 3. npm install && npm run watch (in new terminal)
 - 4. php artisan horizon (in new terminal - need redis-server up and running)
 - 5. php artisan websockets:serve (in new terminal - be sure to read the Set up of Push Notifications section below)


## Key Logins after seed
 - SuperAdmin (developer) u: superadmin@hotboxerp.com p: SuperHotBox2019
 - Admin (2 rec and 1 grow) u: admin@hotboxerp.com p: adminHotBox2019
 - Dispensary Admin (client/owner w 2 rec locations) u: dadmin@hotboxerp.com p: dadminHotBox2019
 - Grow Admin (grow only client) u: sadmin@hotboxerp.com p: sadminHotBox2019
 - Budtender (staff/limited permissions For 1 Dispensary) u: dbudtender@hotboxerp.com p: dbudtenderHotBox2019


## Key Concepts
 - Taxonomy Overview: (all nav registered in superadmin -> data/schema settings -> app_registrar)
   - (Backend - Laravel 5.8) [Service ie: Dispensary/Grow] / [Resource View ie: Categories/Rooms]
   - (Frontend - Vue SPA) [Service] / [Module ie: Products/Harvests] / [Resource View]
   - ** SUB RESOURCES (ie: CategoryMetrc modal for categories, TaxRate) do not have a view, are modals in their respecitive view, and their modal is [Resource][SubResourceName]] camelCase.  ALL main resource names must NOT be camel cased or incluide "_" so their schemas/pages load properly in each vue store module. **
 - The Services / Modules / Views are registered in superadmin schema -> app_registrar (each views "name" MUST MATCH vues & laravels Resource name in code taxonomy!)
 - Each resource has a schema (stored in vuex modules - shipped by Laravel AuthController and handled in vue's store/modules/services classes) which controlls the grid and form of most crud functions
 - Each Client has 1 distro & database.  They toggle between locations (which hold settings)
 - in "application" is where any location settigns / staff and addresses are managed.  as developer, there are also special superadmin controls
 - Grid Filtering: vue-api-query, ie: Dispensary/Category, js/components/views/products/category/grid
   - [schema].filters.type = wherein - list of values of a field, injected in App/Models/[Service]/[Resource]::_getSchema and App/Helpers/Util and schema relaoded when new/edited entries occur while the Grid.vue watches for changes and reloads.
 - Data Normalization - lets try to nortmalize as much as possible (ie: addressbook has types of vendor, location, consumer, Customer has recreational, medical) and for meta data put in a json casted property in model, so that it is easy to add more inpouts pon the fly agilly ie: Location, User, Customer
 - Modal vs Edit page - batchEdit ie: js/components/views/auth/addressbook, sub resource edit modal ie: js/components/views/administration/tax, data view model ie: js/components/views/products/inventory
 - AuthService = all the schema loading (vue calls this when navigating to the various module sections, and on data updating, ie Http/Resources/ for schema injection, authentication and loading vuex
 - Metrc (api) Interaction: there is 1 main service in App/Services/Metrc/MetrcRequestService, with all the "traits" for the diffferent functions called, and log in superadmin/servicelog ie: App/Services/Dispensary/CategoryService::_syncMetrcCategories()

** when you pull, please execute/follow commands in devRefresh.sh.  If you need a reload with sample data, execute/follow commands in devReset.sh - that will load a fresh copy w sample data which is located in database/sql_migrations/hbcloud_sample_data_local.sql **
** Migrations - be sure to always do php artisan migrate when you pull - if you need to create/update database migration files, DO NOT modify ones before 2020_01_21! **


## Application Taxonomy
 - BACKEND App/Http/Controllers/Api/V1/[Service]/[Resource] = all api controller functions for vue and outside api access via oauth token
 - App/Http/Requests/[Service]/[Resource] = handing all request validation and resource data preperation for all endpoints
 - App/Http/Resources/[Service]/[Resource] = review how we handle csv downloading, and schema injection for resources so vue can properly reload..
 - App/Models/[Service]/[Resource] = data modal config, relations, attribution, data parsing, lifecycle hooks, and PREPARING/INJECTING certain schema values (_getSchema)
 - App/Services/[Service]/[Resource] = all logic for interacting with the models and other backend services.
 - FRONTEND resources/js/components = all vue components used.  Layouts are common page parts ie: left menu, Elements are all "dumb" ui componments (take props, emit value)
 - resources/js/components/views/[module]/[resource] = all views: index = module of views, /grid /editForm etc. per view/resource
 - resources/js/routers/[module] = all vue routes, sperated by module in seperate files autoloaded by index, each module is chunked for faster base build of js
 - resources/js/store = vuex store, all modules are in charge of storing the schemas for each resource/view
 - resources/views = laravel blade views; 1 overall for shipping the vue spa, and also for printing / email templates
 - routes/resources = 1 file (autoloaded) per [service]_[resource] api endpoints needed

## Key Libraries Used
 - BACKEND checkout composer.json for all our dependencies
 - FRONTEND typ. Vue2 SPA (vuex, vue-router) https://router.vuejs.org/guide/#html
 - vue-api-query (heavy reliance - resources/js/models/model) https://github.com/robsontenorio/vue-api-query#readme
 - bootstrap-vue (ui elements & frontend framework, especially b-table) https://bootstrap-vue.js.org/docs/components/table/
 - vee-validate (form validation) https://baianat.github.io/vee-validate/
 - vue-multiselect (ui selection inputs) https://vue-multiselect.js.org/
 - sweet-alert https://sweetalert2.github.io/
 - + more in resources/js/services/util.js & package.json

## Set up of Push Notifications
 - Make sure you have updated your .env file to include all of the PUSHER and VAPID settings sections from the .env.example.
 - In your .env make sure that your set BROADCAST_DRIVER to pusher and make up some values for PUSHER_APP_ID, PUSHER_APP_KEY, and PUSHER_APP_SECRET. Also, set PUSHER_APP_SERVER_ADDR to the public ip address of the server.
 - php artisan webpush:vapid (this will generate your VAPID keys required for push notifications. Make sure that they are now in your .env)
 - If you are running this on a remote server you will want to keep the websocket server running. See these instructions <https://docs.beyondco.de/laravel-websockets/1.0/basic-usage/starting.html#keeping-the-socket-server-running-with-supervisord>
 - If SSL is not installed, you will need to configure Chrome in chrome://flags/ setting "Insecure origins treated as secure". Use whatever url you have set up for the environment. Example: Add "http://hotbox.today" to the list of origins in the setting


## Printing set up

### Client set up for label printing
- If using DYMO printer install the most recent version of the SDK for the client OS <https://www.dymo.com/en-US/online-support/online-support-sdk>. By installing the SDK, the DYMO Label Writer Service will be installed. After installing, be sure to click the DYMO Label Writer Service icon in the system tray and click "Diagnose" and in the dialog click "Ok" to accept the certificate
- If using Zebra printer install the most recent version of Zebra Browser Print <https://www.zebra.com/us/en/products/software/barcode-printers/link-os/browser-print.html>. Make sure that Browser Print is loaded on startup OS Login


### Additional server setup steps needed for generating PDFs for printing and email
- sudo apt-get install libfontconfig1:i386
- sudo apt install ttf-mscorefonts-installer
- sudo apt-get install libjpeg62
- echo "deb http://mirrors.kernel.org/ubuntu/ xenial main" | sudo tee -a /etc/apt/sources.list && sudo apt-get update && sudo apt install -y --allow-unauthenticated libpng12-0
- sudo apt install libfontconfig1 libxrender1
- sudo apt install php7.2-pgsql
- cd resources/plugins/wk
- sudo apt install ./libpng12-0_1.2.54-1ubuntu1.1_amd64.deb


## Licenses
 - Laravel: https://laravel-guide.readthedocs.io/en/latest/license/
 - Mysql: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 - Vue: https://opensource.org/licenses/MIT
 - Bootstrap: https://getbootstrap.com/docs/4.0/about/license/
 - nowui (creativetim): 
 - 
