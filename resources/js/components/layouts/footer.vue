<template>
  <footer class="footer">
    <div class="container-fluid d-flex flex-wrap justify-content-between">
        <nav><ul><li><strong>Copyright &copy; {{ getYear() }} {{ $store.state.disp.name }}</strong> All rights reserved. <b>Version: 1.5.1</b></li></ul></nav>
        <!--<div class="copyright d-flex flex-wrap">
            <a href="http://www.hotboxerp.com" target="_blank">Hotbox Solutions</a>
        </div>-->
    </div>
  </footer>
</template>

<script>
    export default {

        methods: {
          getYear(){
            let date = new Date();
            return date.getFullYear();
          }
        },
        mounted(){

        }
    }
</script>
