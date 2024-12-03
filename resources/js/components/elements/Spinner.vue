<template>
    <div v-if="isProcessing"
         class="spinner-container"
         v-bind:class="isFullWidth ? 'fullwidth' : ''">
        <div v-if="isFullScreen"
             class="spinner fullscreen">
            <img v-bind:src="src" v-bind:width="spinnerWidth" alt=""/>
        </div>
        <div v-else
             class="spinner"
             v-bind:class="isFullWidth ? 'fullwidth' : ''">
            <img v-bind:src="src" v-bind:width="spinnerWidth" alt=""/>
        </div>
    </div>
</template>


<script>

export default {

  name: 'Spinner',

  props: {
    isProcessing: {
      type: Boolean,
      default: false
    },
    isFullScreen: {
      type: Boolean,
      default: false
    },
    isFullWidth: {
      type: Boolean,
      default: false
    },
    isLine: {
      type: Boolean,
      default: false
    },
    isLight: {
      type: Boolean,
      default: false
    },
    spinnerWidth: {
      type: Number,
      default: 160
    },

  },

  computed: {
    src() {
        let src = '';
        if (this.isLine) { src = '/fonts/loaders/line'; }
        else { src = '/fonts/loaders/circle'; }
        if (this.isLight) { src += '-light.svg'; }
        else { src += '-dark.svg'; }
        return src;
    }
  }

}

</script>
