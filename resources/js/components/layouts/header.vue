 <template>
    <div id="header-component">
    <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute navbar-page bg-primary">
        <div class="container-fluid">
            <div class="navbar-wrapper">
                <div class="navbar-toggle">
                    <button type="button" class="navbar-toggler" @click="toggleMobileSidebar">
                        <span class="navbar-toggler-bar bar1"></span>
                        <span class="navbar-toggler-bar bar2"></span>
                        <span class="navbar-toggler-bar bar3"></span>
                    </button>
                </div>
                
                <router-link v-if="part" class="navbar-brand" tag="a" :to="{name:'dashboard'}">
                    <span v-if="section">{{ part | ucwords }}</span>
                    <span v-else>{{ $store.state.user.location.name }} Dashboard</span>
                </router-link>
                <router-link v-if="section" class="navbar-brand" tag="a" :to="{name:section.module}">
                    {{ section.name | ucwords }}
                </router-link>
            </div>
<!--            <button class="navbar-toggler" @click="toggleMobileSidebar" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-bar navbar-kebab"></span>
                <span class="navbar-toggler-bar navbar-kebab"></span>
                <span class="navbar-toggler-bar navbar-kebab"></span>
            </button>-->
            <div v-if="section" class="collapse navbar-collapse" id="navigation">
                       
                <ul class="navbar-nav">
                      <router-link v-for="(link, ind) in section.views" v-if="section && !link.hidden  && $store.getters.userCan(link.can_view)" :key="link.name" :to="{name: link.name}" tag="li" class="nav-item"><a class="nav-link">
                          <i :class="link.icon"/>
                            <div class="title-long">{{ link.title || link.tabname }}
                                                      <agg-data :module="section.module"
                                                                :model="link.name"
                                                                :field="link.focus_tab || 'all'"
                                                                type="count" class="" style="display: inline;"/>
                            </div>
                            <div class="title-short">{{ link.title.split(" ").pop() || link.tabname.split(" ").pop() }}
                                                      <agg-data :module="section.module"
                                                                :model="link.name"
                                                                :field="link.focus_tab || 'all'"
                                                                type="count" class="" style="display: inline;"/>
                            </div>

                      </a></router-link>
                </ul>
            </div>
            <div class="nav-actions">
            <div v-if="$store.state.user.location" class="top-toggle">
                <div class="input-group">
                    <ul class="navbar-nav">
                        <li aria-haspopup="true" class="dropdown nav-item btn-rotate dropdown">
                            <a data-toggle="dropdown" aria-haspopup="true" class="nav-link dropdown-toggle">
                                <i :class="{'hotbox-icon hotbox-icon-mjleaf':$store.state.user.location.type.toLowerCase()=='grow','hotbox-icon hotbox-icon-cash-register':$store.state.user.location.type.toLowerCase()=='dispensary'}"></i>
                                <p><span class="d-md-block loc-nav-head">{{ $store.state.user.location.name }} <span v-if="($store.state.user.location||{}).is_demo">**TEST MODE</span></span></p>
                            </a>
                            <ul v-if="$store.state.user.locations_assigned.length>=1" x-placement="" class="dropdown-menu dropdown-menu-right">
                                <a v-for="(location,sid) in $store.state.user.locations_assigned"
                                   v-if="location.id!=$store.state.disp.location"
                                   :key="sid"
                                   class="dropdown-item"
                                   @click.prevent="()=>{assignLocation=location.id; jumpToLocation();}">{{ location.name.substr(0,20) }}</a>
                                <a v-if="$store.getters.userCan('Manage Location')" @click.prevent="()=>{assignLocation='NEW'; jumpToLocation();}" class="dropdown-item">Register a New Location &raquo;</a>
                            </ul>
                        </li>
                    </ul>
                    <ul class="navbar-notifications">
                        <notifications-dropdown :user-id="$store.state.user.id"></notifications-dropdown>
                    </ul>
                </div>
            </div>
            <div v-if="($store.state.user || {}).name" class="user-roles-alert">
                Logged in as {{ $store.state.user.name }}
            </div>
            </div>

        </div>
    </nav>
    <div class="panel-header panel-header-sm" :class="{'panel-header-test-mode':($store.state.user.location||{}).is_demo}"></div>
    
    
    <b-modal v-if="" centered ref="newLocationModal"
        v-model="newLocationModal"
            size="lg"
            header-bg-variant="light"
            header-text-variant="primary">
          
        <template slot="modal-header">
          <i class="modal-top-close fal fa-times" @click="newLocationModal=!newLocationModal"></i>
          <h5 class="w-100 mb-0 text-center">Register a New Location</h5>
        </template>
          
          <register-location-modal v-if="newLocationModal"
                @jump="jumpToNew">
          </register-location-modal>
          
        <template slot="modal-footer">
            <span class="btn-label btn-sm btn-light float-right" @click="newLocationModal=!newLocationModal">Close</span>
        </template>
    </b-modal>

        <b-modal
                v-if="locationStatus!==''"
                modal-class="location-error-modal"
                no-close-on-backdrop
                no-close-on-esc
                ok-disabled
                cancel-disabled
                hide-footer
                :visible="locationStatus!=='activated'"
                centered ref="loyaltyTriggersModal"
                size="lg"
                header-bg-variant="light"
                header-text-variant="primary">

            <template slot="modal-header">
                <h5 class="w-100 mb-0 text-center">
                    <span v-if="locationStatus==='registered'">Location Pending Activation</span>
                    <span v-else-if="locationStatus==='migrating'">Location Pending - Migrating</span>
                    <span v-else>Location Inactive</span>
                </h5>
            </template>

            <template slot="default">
                <p>{{ locationStatusMessage }}</p>

                <div v-if="$store.state.user.locations_assigned.length>=1">
                    <p>Change Your Location:</p>
                    <select v-model="assignLocation"
                            class="form-control"
                            style="margin-bottom: 50px;"
                            v-if="$store.state.user.locations_assigned.length>=1"
                            @change="jumpToLocation()">
                        <option v-for="(location,sid) in userLocations"
                                :key="sid"
                                :value="location.id">{{ (location.id!=$store.state.disp.location) ? 'Go To:' : 'You are at:' }} {{ location.name.substr(0,20) }} {{ (location.is_demo) ? ' **TEST MODE** ' : '' }}
                        </option>
                    </select>
                </div>
                <div class="col-12 mb-4">
                    Or, <a href="/" class=""><b><u>Refresh Page &raquo;</u></b></a>
                </div>
            </template>

        </b-modal>
    
    
    </div>
</template>


<script>

    import RegisterLocationModal from './registerLocationModal';

    export default {
        
        components : { 
            RegisterLocationModal
        },
        
        mounted() {
            this.assignLocation = this.$store.state.disp.location;
        },
        
        updated(){
            this.assignLocation = this.$store.state.disp.location;
        },
        
        data() {
            return {
                assignLocation: null,
                newLocationModal:false,
                messageBag:[]
            };
        },
        
        methods : {
            logout(){
              axios.post('/api/v1/auth/logout').then(response =>  {
                localStorage.removeItem('access_token');
                axios.defaults.headers.common['Authorization'] = null;
                this.$store.dispatch('clearAuthLocation');
                this.$router.replace('/login');
                this.$announcer(response);
              }).catch(error => {
                console.log(error);
              });
            },

            async jumpToLocation(){
                //if any order was pending/cached, send them back to the queue real quick...
                const newLocation = this.assignLocation;
                const cid = this.$store.state['pos']['customerOrder'];

                if (cid) {
                    await axios.get('/api/v1/admin/dispensary/customersqueue/add/'+cid).then(response =>{
                        this.$store.commit('pos/setCustomerOrder', null);
                        return null;
                    })
                }
              if(newLocation=='NEW'){
                  this.newLocationModal = !this.newLocationModal;
                  return false;
              }else{ 
                  return axios.get('/api/v1/admin/auth/locations/change/'+newLocation).then(async response =>  {
                    
                    this.$router.push('/admin/dashboard');
                    
                    await this.$store.dispatch('resetAuthLocation',{scope:'user,sections,agg,message',route:this.$route,reset:true});
                    
                    this.$store.dispatch('administration/setSchemas','customer,tax');
                    this.$store.dispatch('auth/setSchemas','task,addressbook');
                    this.$store.dispatch('loyalty/setSchemas','campaign,discount,group,reward');
                    this.$store.dispatch('outgoing/setSchemas','package,transfer');
                    this.$store.dispatch('plants/setSchemas','harvest,plant,plantbatch');
                    this.$store.dispatch('pos/setSchemas','sale,drawer');
                    this.$store.dispatch('products/setSchemas','product,category,inventory,receiving');
                    this.$store.dispatch('warehouse/setSchemas','item,room,strain');

                    response.data.message = 'You are now at Location '+(this.$store.state.user.locations_assigned.find(e=>e.id===newLocation)||{}).name.substr(0,20) || newLocation;
                    this.$announcer(response);
                  }).catch(error => {
                    this.$announcer(error.response);
                  });
              }
            },
            
            async jumpToNew(loc){
                this.assignLocation = loc;
                await this.jumpToLocation();
                this.newLocationModal=!this.newLocationModal;
            },
            
            goToRoute(route){
              if(route) this.$router.push({name:route});  
            },

            toggleMobileSidebar(){
              this.$emit('toggleMobileSidebar');
            }
        },
        
        computed: {
            userLocations() {
                if (this.$store.state?.user?.locations_assigned.length>0) {
                    return this.$store.state.user.locations_assigned.sort((a,b)=>{return a.name.localeCompare(b.name)});
                }
                return [];
            },

            hasMessageAlert(){
                return (this.$store.state.disp.messageBag.find(v => v.alert===true)) ? true : false;
            },

            part(){
                return (this.$store.state.user.location) ? this.$store.state.user.location.type : null;
            },
            
            section(){
                return (this.$store.state.sections.length && this.$store.state.disp.section) ? this.$store.state.sections.find((v,i)=>this.$store.state.disp.section==i) : null;
            },
            
            page(){
                return (this.section) ? this.section.views.find(v=>v.name==this.$route.name) : null;
            },

            locationStatus() {
                // registered,activated,held,denied,inactivate
                return (this.$store.state.user && this.$store.state.user.location && this.$store.state.user.location.status) ? this.$store.state.user.location.status : '';
            },

            locationStatusMessage() {
                let s = this.$store.state.user.location.name;
                const defaultMessage = 'Please contact your admin for help.';

                switch (this.locationStatus) {
                    case 'migrating' : s+=' is currently undergoing migration.  Please wait until all data has migrated.';break;
                    case 'registered': s+=` is pending activation. ${defaultMessage}`;break;
                    case 'denied' : s+=` has been denied. ${defaultMessage}`;break;
                    case 'held' : s+=` is on hold. ${defaultMessage}`;break;
                    default: s+=` is inactive. ${defaultMessage}`;break;
                }
                return s;
            }
        },
        
        watch: {
            '$store.state.disp.location'(to,from){
                if(to) this.assignLocation = this.$store.state.disp.location;
            },
        }
    }
</script>

<style scoped>
/*    .top-toggle {
        margin-top:0;
    }*/
    .top-toggle .input-group {
        justify-content: flex-end;
    }
    .top-toggle .input-group .location-select {
        height: unset;
    }
    .top-toggle .input-group:focus-within .input-group-prepend {
      border: 1px solid #f96332;
      border-right: 0;
    }
    .top-toggle .input-group:focus-within .input-group-append {
      border: 1px solid #f96332;
      border-left: 0;
    }
    .top-toggle .input-group:focus-within > select {
      border: 1px solid #f96332;
      border-right: 0;
      border-left: 0;
    }
    .top-toggle .form-control:focus + .input-group-append .input-group-text {
        border: 0;
    }
    .top-toggle .input-group .input-group-append,
    .top-toggle .input-group .input-group-prepend {
        transition: color 0.3s ease-in-out, border-color 0.3s ease-in-out, background-color 0.3s ease-in-out;
    }
    .top-toggle .location-select:focus {
        background-color: transparent;
    }
    .top-toggle .location-select:focus option {
        background: #27507d;
    }
    
    .top-toggle .location-test-select:focus option{
        background: #6b1313 !important;
    }

    .loc-nav-head{
        font-size:1.2em;
        font-weight:700;
    }
    .notification-icon{
        margin-right: 3px;
        font-size: 1.2em;
    }

    @media (max-width: 1200px) {
        .navbar-collapse {
            display: none !important;
        }
    }
    @media (max-width: 992px) {
        #header-component {
            padding-bottom: 10px;
        }

    }

    .user-roles-alert {
        font-size: 0.8rem;
        position: absolute;
        right: 32px;
    }

    .panel-header-test-mode {
        background: linear-gradient(to right, #6b1313 0%, #9b0000 60%, #9b0000 100%);
    }

    .dropdown-menu {
        border-radius: 12px;
        padding: 0;
        cursor: pointer;
        border: 0 none;
        margin-top: 10px;
    }
    .dropdown-item {
        margin: 0;
    }
    .dropdown-item:hover {
        color: #fff!important;
        opacity: 1;
        text-decoration: none;
        background-color: #96abc2;
    }
    .dropdown-menu .dropdown-item:first-child {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .dropdown-menu .dropdown-item:last-child {
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .navbar-notifications {
        margin: auto 0;
        padding: 0;
        font-size: .8rem;
    }
    .navbar-notification .notification-icon {
        margin-right:0;
    }

@media screen and (max-width: 991px) {
    .navbar-nav {
        height: 65px;
    }
    .user-roles-alert {
        margin-top: -20px;
    }
    .dropdown-menu {
        background-color: #fff !important;
        margin: 0 !important;
        z-index: 1000;
        position: absolute;
    }
    .dropdown.nav-item.btn-rotate.dropdown.show .dropdown-menu.dropdown-menu-right.show::before {
        display: inline-block;
        position: absolute;
        width: 0;
        height: 0;
        vertical-align: middle;
        content: "";
        top: -5px;
        color: #FFFFFF;
        border-bottom: 0.4em solid;
        border-right: 0.4em solid transparent;
        border-left: 0.4em solid transparent;
    }
}
@media screen and (max-width: 1000px) {
    .show-mobile-sidebar .nav-actions {
        position: absolute;
        right: 10px;
        top: 40px;
    }
    .show-mobile-sidebar .user-roles-alert {
        display: none;
    }
}
@media screen and (max-width: 800px) {
    :not(.show-mobile-sidebar) .nav-actions {
        position: absolute;
        right: 10px;
        top: 40px;
    }
    :not(.show-mobile-sidebar) .user-roles-alert {
        display: none;
    }
}
</style>