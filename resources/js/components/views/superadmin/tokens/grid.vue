<template>
	<div>
	    
	    <div class="col-12 mt-2 mb-4">
	        <h5 class="mt-3">Manage Clients:</h5>
	        <clients></clients>
	    </div>
	    
	    <div class="col-12 mt-2 mb-4">
	        <h5 class="mt-3">Manage Personal Acess Tokens:</h5>
	        <personal-access-tokens></personal-access-tokens>
	    </div>
	    
	    <div class="col-12 mt-2 mb-4">
	        <h5 class="mt-3">Authorized Clients:</h5>
	        <authorized-clients></authorized-clients>
	    </div>

    </div>
</template>

<script>

    import AuthorizedClients from './AuthorizedClients';
    import Clients from './Clients';
    import PersonalAccessTokens from './PersonalAccessTokens';
    
    export default {
        
        components:{ 
            AuthorizedClients,Clients,PersonalAccessTokens
        },
        
        data() {
            return {

            };
        },

        mounted() {

        },

        methods: {

        }
    }
</script>

<style>
    
</style>
