<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Helper\SymfonyQuestionHelper;
use Symfony\Component\Console\Question\Question;

use App\Models\AppSchema;

use DB;
use File;


class GenerateAppSuite extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sysadmin:genappsuite {--force : Overwrite existing views by default}';

    /**S
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installation of a Location App Section/View [module/model]';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;


    protected $section;
    protected $module;
    protected $model;
    
    protected $app;
    protected $schema;
    protected $schemaDate;
    protected $exportHeader;
    protected $modelFillable;
    protected $modelSchemaFilters;
    protected $modelFillMigration;
    protected $moduleVueViews;
    protected $moduleSchemaList;


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
        
        $this->line('------------------------------------------');
        $this->line('Initalizing AppSuite Module Installation..');
        $this->line('------------------------------------------');

        $extensions = get_loaded_extensions();
        $require_extensions = ['mbstring', 'openssl', 'curl', 'exif', 'fileinfo', 'tokenizer'];
        foreach (array_diff($require_extensions, $extensions) as $missing_extension)
            $this->error('Missing '.ucfirst($missing_extension).' extension');

        /* init installation arguments */
        $force = ($this->option('force') ? true : false);
        $this->service = $this->ask('App Service Name (ucwords app namespace)');
        $this->module = $this->ask('Module (ucwords keyname of App Section):');
        $this->model = $this->ask('Model (ucwords keyanme of model resource from schema');
        $secret = $this->ask('PassPhrase:');


        if(!$secret || !$this->module || !$this->model) $this->error('Whoops missing prompts - aborting');
        elseif($secret != config('app.adminpin')) $this->error('Whoops incorrect Admin Pin - aborting');
        elseif(($this->app = data_get(AppSchema::getSchema('app_registrar'),strtolower($this->service),null)) == null) $this->error('Whoops the module app data is not yet registered!');
        elseif(($this->section = collect((array)$this->app)->firstWhere('module',strtolower($this->module))) == null)  $this->error('Whoops no [submenu] section registered!');
        elseif(($this->schema = AppSchema::getSchema(strtolower($this->model).'_schema')) == null) $this->error('Whoops the model resource schema data is not yet registered!');
        elseif(($this->schemaDate = AppSchema::getSchemaDate(strtolower($this->model).'_schema')) == null) $this->error('Whoops the model resource schema data is not yet registered!');
        else{

        /* set dynamic text parsers */
        $this->setExportHeader();
        $this->setModelFillable();
        $this->setModelSchemaFilters();
        $this->setModelFillMigration();
        $this->setModuleVueViews();
        $this->setModuleSchemaList();


        /* Api Controller */
        if(!file_exists(app_path("Http/Controllers/Api/V1/{$this->service}")))
            mkdir(app_path("Http/Controllers/Api/V1/{$this->service}"), 0777, true);   // module controller directory
            
        if(!file_exists(app_path("Http/Controllers/Api/V1/{$this->service}/{$this->model}Controller.php")) || $force==true)
            file_put_contents(app_path("Http/Controllers/Api/V1/{$this->service}/{$this->model}Controller.php"), $this->_getStub('Controller'));
        
        
        /* Request Validation Classes */
        if(!file_exists(app_path("Http/Requests/{$this->service}")))
            mkdir(app_path("Http/Requests/{$this->service}"), 0777, true);       // module requests directory        
        
        if(!file_exists(app_path("Http/Requests/{$this->service}/{$this->model}UpdateRequest.php")) || $force==true)
            file_put_contents(app_path("Http/Requests/{$this->service}/{$this->model}UpdateRequest.php"), $this->_getStub('UpdateRequest'));


        /* response toArray classes */
        if(!file_exists(app_path("Http/Resources/{$this->service}")))
            mkdir(app_path("Http/Resources/{$this->service}"), 0777, true);       // module resources directory  
        
        if(!file_exists(app_path("Http/Resources/{$this->service}/{$this->model}Resource.php")) || $force==true)
            file_put_contents(app_path("Http/Resources/{$this->service}/{$this->model}Resource.php"), $this->_getStub('Resource'));

        if(!file_exists(app_path("Http/Resources/{$this->service}/{$this->model}CollectionResource.php")) || $force==true)
            file_put_contents(app_path("Http/Resources/{$this->service}/{$this->model}CollectionResource.php"), $this->_getStub('CollectionResource'));        
        
        if(!file_exists(app_path("Http/Resources/{$this->service}/{$this->model}CollectionExport.php")) || $force==true)
            file_put_contents(app_path("Http/Resources/{$this->service}/{$this->model}CollectionExport.php"), $this->_getStub('CollectionExport'));
        
        
        /* the Model class */
        if(!file_exists(app_path("Models/{$this->service}")))
            mkdir(app_path("Models/{$this->service}"), 0777, true);              // models directory          
        
        if(!file_exists(app_path("Models/{$this->service}/{$this->model}.php")) || $force==true)
            file_put_contents(app_path("Models/{$this->service}/{$this->model}.php"), $this->_getStub('Model'));
        
        
        /* the service class */
        if(!file_exists(app_path("Services/{$this->service}")))
            mkdir(app_path("Services/{$this->service}"), 0777, true);              // services directory          
        
        if(!file_exists(app_path("Services/{$this->service}/{$this->model}Service.php")) || $force==true)
            file_put_contents(app_path("Services/{$this->service}/{$this->model}Service.php"), $this->_getStub('Service'));        
        
        
        /* the migration file USING DATE OF SCHEMA RESOURCE SO AS TO NOT DUPLICATE */
        if(!file_exists(base_path('database/migrations/'.date('Y_m_d_His',$this->schemaDate->timestamp).'_create_'.strtolower($this->service).'_'.strtolower(str_plural($this->model)).'_table.php')) || $force==true)
            file_put_contents(base_path('database/migrations/'.date('Y_m_d_His',$this->schemaDate->timestamp).'_create_'.strtolower($this->service).'_'.strtolower(str_plural($this->model)).'_table.php'), $this->_getStub('Migration'));  
        
        
        /* the API routes (each model respource has a [module]_[model].php route in the autoloaded appsuite folder grouped to its api namespace. */
        if(!file_exists(base_path('routes/resources/'.strtolower($this->service).'_'.strtolower($this->model).'.php')) || $force==true)
            file_put_contents(base_path('routes/resources/'.strtolower($this->service).'_'.strtolower($this->model).'.php'), $this->_getStub('Route')); 
        
        

        $this->info("AppSuite Backend(laravel) Functions Registered.");
        /* Moving on to the Vue Frontend..*/
        
        /* vue-api-query Model file and veux Store Module */
        if(!file_exists(resource_path("js/models/{$this->model}.js")) || $force==true)
            file_put_contents(resource_path("js/models/{$this->model}.js"), $this->_getStub('VueModel'));    
        
        //if(!file_exists(resource_path('js/store/modules/'.strtolower($this->module).'.js')) || $force==true)
            file_put_contents(resource_path('js/store/modules/'.strtolower($this->module).'.js'), $this->_getStub('VueModule'));  

        
        /* vue view components */
        if(!file_exists(resource_path('js/components/views/'.strtolower($this->module))))
            mkdir(resource_path('js/components/views/'.strtolower($this->module)), 0777, true);  // module controller directory
        if(!file_exists(resource_path('js/components/views/'.strtolower($this->module).'/'.strtolower($this->model))))
            mkdir(resource_path('js/components/views/'.strtolower($this->module).'/'.strtolower($this->model)), 0777, true);  // module controller directory
        
        if(!file_exists(resource_path('js/components/views/'.strtolower($this->module).'/index.vue')) || $force==true)
            file_put_contents(resource_path('js/components/views/'.strtolower($this->module).'/index.vue'), $this->_getStub('VueIndex')); // Module index vue

        if(!file_exists(resource_path('js/components/views/'.strtolower($this->module).'/'.strtolower($this->model).'/grid.vue')) || $force==true)
            file_put_contents(resource_path('js/components/views/'.strtolower($this->module).'/'.strtolower($this->model).'/grid.vue'), $this->_getStub('VueGrid')); // grid page     

        if(!file_exists(resource_path('js/components/views/'.strtolower($this->module).'/'.strtolower($this->model).'/editForm.vue')) || $force==true)
            file_put_contents(resource_path('js/components/views/'.strtolower($this->module).'/'.strtolower($this->model).'/editForm.vue'), $this->_getStub('VueEditForm')); //create / edit form page

        if(!file_exists(resource_path('js/components/views/'.strtolower($this->module).'/'.strtolower($this->model).'/batchEditModal.vue')) || $force==true)
            file_put_contents(resource_path('js/components/views/'.strtolower($this->module).'/'.strtolower($this->model).'/batchEditModal.vue'), $this->_getStub('VueBatchEditModal')); // batch (mass) edit modal

        
        /* And Finally, add vue-router [layz-loaded] routes */
        //if(!file_exists(resource_path('js/routers/'.strtolower($this->module).'.js')) || $force==true)
            file_put_contents(resource_path('js/routers/'.strtolower($this->module).'.js'), $this->_getStub('VueRouter'));          



        /* refresh app */
        $this->info("AppSuite Frontend(vue) Functions Registered.");
        

        /* return sucess */
        $this->line("AppSuite Section/view [{$this->service} {$this->module}/{$this->model}] Installation Complete - Be sure to do a devRefresh!");
        //$this->line('Installation Complete');
        //$this->comment('Installation Complete');
        //$this->question('Installation Complete');
        //$this->error('Installation Complete');
        
        }

    }
    
    
    /* parse stub with module/model and schema data */
    protected function _getStub($typ='Controller'){
        

        return str_replace(
            [
                '{{model}}',
                '{{modelLowerCase}}',
                '{{modelPlural}}',
                '{{modelPluralLowerCase}}',
                '{{modelSingularLowerCase}}',
                '{{module}}',
                '{{moduleLowerCase}}',
                '{{modelExportHeader}}',
                '{{modelFillable}}',
                '{{modelSchemaFilters}}',
                '{{modelFillMigration}}',
                '{{moduleVueViews}}',
                '{{moduleSchemaList}}',
                '{{service}}',
                '{{serviceLowerCase}}'
            ],
            [
                $this->model,
                strtolower($this->model),
                str_plural($this->model),
                strtolower(str_plural($this->model)),
                strtolower($this->model),
                $this->module,
                strtolower($this->module),
                $this->exportHeader,
                $this->modelFillable,
                $this->modelSchemaFilters,
                $this->modelFillMigration,
                $this->moduleVueViews,
                $this->moduleSchemaList,
                $this->service,
                strtolower($this->service)
            ],
            file_get_contents(resource_path("stubs/{$typ}.stub"))
        );
        
    }
    
    
    protected function setExportHeader(){                                       // header text for export collection via schema form (csv)
        
        $this->exportHeader = '';
        foreach((array)data_get($this->schema,'form',[]) as $key => $data)
            $this->exportHeader .= "                '{$key}' => '".data_get($data,'title',ucwords($key))."',\n";

    }

    protected function setModelFillable(){                                       // header text for export collection via schema form (csv)
        
        $this->modelFillable = "'".join("','",array_keys((array)data_get($this->schema,'form',[])))."'";

    }
    
    protected function setModelSchemaFilters(){
        
        $this->modelSchemaFilters = '';
        foreach((array)data_get($this->schema,'filters',[]) as $key => $data)
            if(in_array($data->type,['wherein','daterange']))
                $this->modelSchemaFilters .= ($data->type=='daterange' ? '        data_set($schema,\'filters.'.$key.'.values\',Util::getActiveFilterDateRange(\'SOM\'),true);'."\n" : 
                    '        data_set($schema,\'filters.'.$key.'.values\',Util::getActiveFilterList(self::query()->ofActive(),$schema,\''.$key.'\'),true);'."\n");

    }
    
    protected function setModelFillMigration(){
        
        $this->modelFillMigration = '';
        foreach((array)data_get($this->schema,'form',[]) as $key => $data){
            if(in_array($key,['id','suite_id','name','archived_at','created_at','updated_at'])) continue; //already exist in stub
            elseif(!isset($data->type)) continue;
            switch($data->type){
                case 'select':
                case 'multiselect':
                case 'simpleselect':
                case 'customselect':
                    $this->modelFillMigration .= '            $table->enum(\''.$key.'\', [\''.
                        join("','",collect((array)data_get($data,'values',[]))->map(function($v,$k){ return $v->id; })->toArray())
                    .'\'])->index()->nullable();'."\n";
                    break;
                case 'text':
                    $this->modelFillMigration .= '            $table->text(\''.$key.'\')->nullable();'."\n";
                    break;
                case 'textarea':
                    $this->modelFillMigration .= '            $table->text(\''.$key.'\')->nullable();'."\n";
                    break;
                case 'number':
                    $this->modelFillMigration .= '            $table->decimal(\''.$key.'\',10,4)->nullable();'."\n";
                    break;
                case 'datetime':
                    $this->modelFillMigration .= '            $table->dateTime(\''.$key.'\')->nullable();'."\n";
                    break;
                default: 
                    $this->modelFillMigration .= '            $table->text(\''.$key.'\')->nullable();'."\n";
            }
        }
        
    }
    
    protected function setModuleVueViews(){
        
        $this->moduleVueViews = '[
                    {
                        path: \'/admin/'.strtolower($this->module).'\',
                        component:  () => import(/* webpackChunkName: "js/view-'.strtolower($this->service).'" */ \'../components/views/'.strtolower($this->module).'/'.strtolower($this->section->views[0]->name).'/grid\'),
                        name: \''.strtolower($this->module).'_index\'
                    },';
                    
        foreach((array)data_get($this->section,'views',[]) as $view){
          if(data_get($view,'focus_tab',null)==null)
            $this->moduleVueViews .= '
                    {
                        path: \'/admin/'.strtolower($this->module).'/'.strtolower($view->name).'\',
                        component:  () => import(/* webpackChunkName: "js/view-'.strtolower($this->service).'" */ \'../components/views/'.strtolower($this->module).'/'.strtolower($view->name).'/grid\'),
                        props: true,
                        name: \''.strtolower($view->name).'\'
                    },
                    {
                        path: \'/admin/'.strtolower($this->module).'/'.strtolower($view->name).'/create\',
                        component:  () => import(/* webpackChunkName: "js/view-'.strtolower($this->service).'" */ \'../components/views/'.strtolower($this->module).'/'.strtolower($view->name).'/editForm\'),
                        props: true,
                        name: \''.strtolower($view->name).'_create\'
                    },  
                    {
                        path: \'/admin/'.strtolower($this->module).'/'.strtolower($view->name).'/:id/edit\',
                        component:  () => import(/* webpackChunkName: "js/view-'.strtolower($this->service).'" */ \'../components/views/'.strtolower($this->module).'/'.strtolower($view->name).'/editForm\'),
                        props: true,
                        name: \''.strtolower($view->name).'_edit\'
                    },';
        }
        
        
        $this->moduleVueViews .= ']';
        
    }
    
    protected function setModuleSchemaList(){
        
        $_list = [];
        foreach((array)data_get($this->section,'views',[]) as $view)
          if(data_get($view,'focus_tab',null)==null)
            $_list[] = strtolower($view->name).'Schema: null';
            
        $this->moduleSchemaList = join(",\n",$_list);
            
    }
    
    

}