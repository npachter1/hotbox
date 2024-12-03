<template>
	<div>
		<p v-if="$route.name!='register'" class="text-center">
			<small>
				<router-link to="/password" class="text-info m-l-5">Forgot Your Password?</router-link> | By <a href="https://hotboxerp.com" target="_blank">Hotbox ERP, Inc.</a>
			</small>
		</p>
	</div>
</template>


<script>
    export default { }
</script>