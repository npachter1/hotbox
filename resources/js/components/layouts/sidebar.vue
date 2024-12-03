<template>
    <div class="sidebar" :class="{'show-menu': showMobileSidebar}" data-color="black">
        <div class="logo">
            <a href="/" class="simple-text logo-mini">
                <div class="logo-image"><img src="/images/logo.png"></div>
            </a>
            <a href="/admin/dashboard" class="simple-text logo-normal">
                Hotbox
            </a>
            <div class="navbar-minimize">
                <button id="minimizeSidebar" class="btn btn-simple btn-hotbox-icon btn-neutral btn-round" @click="toggleMiniSidebar">
                    <i class="now-ui-icons text_align-center visible-on-sidebar-regular"></i>
                    <i class="now-ui-icons design_bullet-list-67 visible-on-sidebar-mini"></i>
                </button>
            </div>
        </div>
        <div class="sidebar-wrapper" ref="sidebarScrollArea">
            <ul class="nav">
                <li class="menu-section"
                    v-for="(section,sid) in $store.state.sections"
                    v-if="sectionsUi && !section.hidden && $store.getters.userCan(section.can_view)"
                    :ref="'menu-section-' + sid"
                    :class="{'active':sid==$store.state.disp.section,'test-mode':($store.state.user.location||{}).is_demo}"
                    :key="section.module">
                    <a id="collapseLink"
                       data-toggle-legacy="collapse"
                       @click="clickSection(sid)"
                       :href="'#'+section.module+'MenuCollapse'"
                       class="collapse-link"
                       :class="{'collapsed':!sectionActive(sid)}"
                       :aria-expanded="sectionActive(sid) ? 'true' : 'false'">
                        <i :class="section.icon" class="section-icon"></i>
                        <p>{{ section.name }} <b class="caret"></b></p>
                    </a>
                    <b-collapse v-model="sectionsUi[sid].active" class="collapse" :class="{'show':sectionActive(sid)}" :id="section.module+'MenuCollapse'">
                        <ul class="nav">
                            <router-link class="menu-section" tag="li"
                                         :to="{name: view.name}"
                                         v-if="!view.hidden && $store.getters.userCan(view.can_view)"
                                         v-for="(view,vid) in section.views"
                                         @click.native="collapseInactive(sid)"
                                         :key="vid">
                                <a class="section-item">
                                    <span class="sidebar-mini-icon" @click="collapseInactive(sid)"><i :class="view.icon"></i></span><span class="sidebar-normal"> {{ view.title || view.tabname }}</span>
                                </a>
                            </router-link>

                            <li v-if="section.module=='auth'" class="menu-section">
                                <router-link v-if="$store.getters.userCan('Manage Server')" class="menu-section" tag="a" :to="{name:'servicelog'}">
                                    <span class="sidebar-mini-icon"><i class="ti-panel"></i></span><span class="sidebar-normal"> SuperAdmin Console</span>
                                </router-link>
                            </li>
                            <li v-if="section.module=='auth'" class="menu-section mt-2">
                                <a href="" class="" @click.prevent="logout">
                                    <span class="sidebar-mini-icon"><i class="ti-power-off"></i></span><span class="sidebar-normal"> Logout</span>
                                </a>
                            </li>
                        </ul>
                    </b-collapse>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import 'perfect-scrollbar/css/perfect-scrollbar.css';
    import PerfectScrollbar from 'perfect-scrollbar';
    import { BCollapse } from 'bootstrap-vue';

    export default {
        props: {
            showMobileSidebar: {
                type: Boolean,
                description: "Whether sidebar is visible on mobile"
            }
        },

        components: {
            BCollapse
        },

        computed: {

        },

        data () {
            return {
                scrollbar: null,
                assignLocation:null,
                sectionsUi: null,
            };
        },

        methods: {
            sectionActive(sid) {
                return this.sectionsUi && this.sectionsUi[sid] && this.sectionsUi[sid].active;
            },
            collapseInactive(sid) {
                this.sectionsUi.forEach((e,i) => {
                    e.active = (sid===i)
                });
            },
            clickSection(sid) {
                let anySectionsActive = false;
                this.sectionsUi.forEach((e,i) => {
                    e.active = sid===i ? !e.active : false;
                    if (e.active) anySectionsActive=true;
                });
                setTimeout(()=> {
                    if (!anySectionsActive) {
                        this.$refs.sidebarScrollArea.scrollTop = 0;
                    } else {
                        if (document.getElementsByClassName('menu-section active').length>0) document.getElementsByClassName('menu-section active')[0].scrollIntoView();
                    }
                }, 10);
            },
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

            toggleMiniSidebar() {
                this.$emit('toggleMiniSidebar');
            },

            toggleMode(){
                axios.get('/api/v1/admin/auth/toggleMode').then(async response =>  {
                    await this.$store.dispatch('resetAuthLocation',{scope:'user,sections,agg,message',route:this.$route,reset:true});
                    response.data.message = 'Your location is now in '+((this.$store.state.user.location.is_demo) ? 'TEST mode' : 'LIVE mode');
                    this.$announcer(response);
                }).catch(error => {
                    this.$announcer(error.response);
                });
            },

            updateScrollbar() {
                setTimeout(()=> {
                    this.scrollbar.update();
                }, 10);
            },
            setupSections() {
                if (this.$store.state.sections) {
                    this.sectionsUi = this.$store.state.sections.map((e,i)=>{
                        return {
                            name:e.name,
                            id:i,
                            active:i===this.$store.state.disp.section
                        }
                    });
                }
            }
        },
        mounted () {
            this.setupSections();

            this.scrollbar = new PerfectScrollbar(this.$refs.sidebarScrollArea);

            this.$nextTick(() => {
                window.addEventListener('resize', this.updateScrollbar);
            });


        },
        watch: {
            '$store.state.sections'(to){
                if (to && to.length>0) this.setupSections();
            },
        },
    };

</script>

<style>
    .sidebar .sidebar-wrapper {
        overflow: hidden;
    }

    div.sidebar.show-menu {
        transform: unset;
    }

    .sidebar-mini .sidebar:not(:hover) .sidebar-wrapper > .nav [data-toggle-legacy=collapse] ~ div > ul > li > a .sidebar-mini-icon {
        margin-left: 4px !important;
        margin-top: 3px;
    }

    div.sidebar div.sidebar-wrapper ul.nav li.menu-section.active > a.collapse-link,
    div.sidebar div.sidebar-wrapper ul.nav li.menu-section.active > a.collapse-link > i.section-icon {
        color:#fff;
    }

    li.menu-section a:not(.collapsed) b.caret {
        transform: rotate(180deg);
    }
    div.sidebar div.sidebar-wrapper {
        padding-bottom: 0;
    }
    
    .test-mode a i{
        color: #a56262 !important
    }
</style>