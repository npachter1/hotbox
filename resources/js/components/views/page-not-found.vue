<template>
	<section id="wrapper" class="error-page">
        <div class="error-box">
            <div class="error-body text-center mt-4 mb-4">
                <h1>Whoops</h1>
                <h3 class="text-uppercase">You may have wandered too far [404]!</h3>
                <p class="text-muted m-t-30 m-b-30">The page you have requested is either moved away or doesn't exist.</p>
                <router-link to="/admin/dashboard" class="btn btn-info btn-rounded m-b-40">Back to Dashboard</router-link> 
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        mounted() {
            
        }
    };
</script>