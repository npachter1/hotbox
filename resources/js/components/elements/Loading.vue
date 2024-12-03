<template>
    <loader v-if="type=='loadAsset'"
      :active="display"
      spinner="bar-fade-scale" 
      size="112" 
      text="Loading.." 
      :text-style="{fontSize:'1.5em',color:'rgba(53, 73, 94, 0.58)'}" 
      color="rgba(53, 73, 94, 0.58);" 
      duration="0.8" 
      :is-full-screen="false" />
    
    <div v-else-if="type=='loadGrid'" class="load-top-grid" :class="{'d-none':!display,'d-block':display}">
      <div>
        <loader
          :active="display"
          spinner="bar-fade" 
          size="55" 
          text="Processing.." 
          :text-style="{fontSize:'1.2em',color:'rgba(53, 73, 94, 0.87)'}" 
          color="rgba(53, 73, 94, 0.58);" 
          duration="1.2"
          :is-full-screen="false" />
      </div>
    </div>

    <loader v-else-if="type=='loadAll'"
      :active="display"
      spinner="bar-fade-scale" 
      size="150" 
      color="rgba(53, 73, 94, 0.58);" 
      duration="1.2" 
      :is-full-screen="true" />

    <loader v-else-if="type=='loadModal'"
      :active="display"
      spinner="spinner" 
      size="65" 
      text="Loading.." 
      :text-style="{fontSize:'1.2em',color:'rgba(53, 73, 94, 0.58)'}" 
      color="rgba(53, 73, 94, 0.58);" 
      duration="1.0" 
      :is-full-screen="false" />

    <loader v-else-if="type=='loadPage'"
      :active="display"
      spinner="spinner" 
      size="85" 
      color="rgba(53, 73, 94, 0.58)" 
      duration="2.0" 
      :is-full-screen="false" />
    
    <loader v-else-if="type=='loadInline'"
      :active="display"
      spinner="bar-fade" 
      size="18" 
      duration="1.5" 
      :is-full-screen="false" />
      
    <loader v-else 
      :active="display"
      spinner="spinner" 
      size="100" 
      text="Loading.." 
      :text-style="{fontSize:'1.5em',color:'rgba(53, 73, 94, 0.58)'}" 
      color="rgba(53, 73, 94, 0.58);" 
      duration="1.0" 
      :is-full-screen="false" />
</template>

<script>
  
  import Loader from 'vue-element-loading';                                     // loading within an element library
  
  export default {
    components: { Loader },
    props: {
      type: {
        type: String,
        default: 'loadGrid'
      },
      display: Boolean,
      default: false
    }
  }
</script>

<style>

</style>