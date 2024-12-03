<template>
    <div class="wrapper" :class="{'sidebar-mini': isMiniSidebar}">
        <app-sidebar @toggleMiniSidebar="isMiniSidebar=!isMiniSidebar" :showMobileSidebar="showMobileSidebar"></app-sidebar>
        <transition name="hotbox-fade" mode="out-in">
            <div class="main-panel" :class="{'show-mobile-sidebar':showMobileSidebar}">
                <app-header @toggleMobileSidebar="showMobileSidebar=!showMobileSidebar"></app-header>
                <div class="content">
                    <notifications></notifications>
                    <nprogress-container></nprogress-container>
                    <router-view></router-view>
                </div>
                <app-footer></app-footer>
            </div>
        </transition>
    </div>
</template>

<script>
    
    import AppHeader from './header.vue';
    import AppSidebar from './sidebar.vue';
    import AppFooter from './footer.vue';
    import NprogressContainer from 'vue-nprogress/src/NprogressContainer';

    export default {
      data () {
        return {
          isMiniSidebar: false, //for desktop, the narrow sidebar version
          showMobileSidebar: false, //for mobile, where sidebar is hidden by default
        };
      },

        methods : {

        },
        
        components: {
            AppHeader, AppSidebar, AppFooter,NprogressContainer
        },
        
        mounted(){

        },
        
        watch: {
          isMiniSidebar: function(newValue, oldValue) {
            if (newValue) this.showMobileSidebar=false;
          },
          showMobileSidebar: function(newValue, oldValue) {
            if (newValue) this.isMiniSidebar=false;
          }
        },
        
        created() {

        }
    };
</script>

<style>
    div.main-panel.show-mobile-sidebar {
        width: calc(100% - 260px)
    }
</style>
