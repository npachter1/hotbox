<template>
    <div class="wrapper">
        <notifications></notifications>
    	<router-view></router-view>
    </div>
</template>

<script>

    export default {
        
        methods : {

        },
        mounted() {
        	$('body').removeClass('fix-header fix-sidebar card-no-border');
        },
        destroyed(){
            
        }
    }
</script>