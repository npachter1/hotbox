<template>
    <div class="button-inline">
        <a v-if="!disabled" @click.prevent="$emit('click')" class="btn btn-md btn-danger ml-2"><i class="hotbox-icon hotbox-icon-trash-round"></i> {{ text }}.</a>

        <div v-else-if="showDisabled" class="disabled-container" @click="showDialog">
            <a disabled class="btn btn-md btn-danger ml-2 disabled-link text-white"><i v-if="iconClass!==''" :class="iconClass"></i> {{ text }}.</a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "FormButton",
        props: {
            text: {
                type: String,
                default: '',
            },
            disabled: {
                type: Boolean,
                default: false,
            },
            showDisabled: {
                type: Boolean,
                default: false,
            },
            disabledText: {
                type: String,
                default: 'This Option is Unavailable',
            },
            disabledTitle: {
                type: String,
                default: ''
            },
            iconClass: {
                type: String,
                default: 'hotbox-icon hotbox-icon-trash-round'
            }
        },
        methods: {
            showDialog() {
                this.$swal.fire({
                    text: this.disabledText,
                    title: this.disabledTitle,
                    type: 'warning',
                    confirmButtonText: 'OK',
                })
            }
        }
    }
</script>

<style scoped>
    .button-inline {
        display: inline;
    }
    .disabled-container {
        display: inline;
        cursor: pointer;
    }
    .disabled-link {
        pointer-events: none; /*for a disabled element, this bubbles the click event to parent*/
    }
</style>