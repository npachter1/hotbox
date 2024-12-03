<template>
  <div class="form-check form-check-radio mt-0" :class="[inlineClass, {disabled: disabled}]">
    <label :for="cbId" class="form-check-label pl-2">
      <input :id="cbId"
             class="form-check-input"
             type="radio"
             :disabled="disabled"
             :value="label"
             :name="name"
             v-model="model" />
      <span class="form-check-sign">
      </span>
      <slot></slot>
    </label>
  </div>
</template>
<script>
  export default {
    name: 'n-radio',
    props: {
      label: [String, Number],
      disabled: Boolean,
      value: [String, Boolean],
      inline: Boolean,
      name: String,
    },
    data () {
      return {
        cbId: ''
      }
    },
    computed: {
      model: {
        get () {
          return this.value
        },
        set (value) {
          this.$emit('input', value)
        }
      },
      inlineClass () {
        if (this.inline) {
          return `form-check-inline`
        }
        return ''
      }
    },
    created () {
      this.cbId = Math.random().toString(16).slice(2)
    }
  }
</script>
