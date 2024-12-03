<template>
    <svg xmlns="http://www.w3.org/2000/svg"
         :width="width"
         :height="height"
         viewBox="0 0 32 32"
         :aria-labelledby="iconName"
         role="presentation">
        <title :id="iconName" :lang="'en'">{{ iconName }}</title>
        <g :fill="iconColor">
          <slot />
        </g>
  </svg>
</template>

<script>
    // lifted from here:
    //   https://vuejs.org/v2/cookbook/editable-svg-icons.html
    //   https://github.com/sdras/vue-sample-svg-icons

    export default {
        props: {
            iconName: {
                type: String,
                default: 'box'
            },
            width: {
                type: [Number, String],
                default: 32
            },
            height: {
                type: [Number, String],
                default: 32
            },
            iconColor: {
                type: String,
                default: 'currentColor'
            }
        }
    }
</script>

<style scoped>
    svg {
        display: inline-block;
        vertical-align: baseline;
        margin-bottom: -2px;
    }
</style>