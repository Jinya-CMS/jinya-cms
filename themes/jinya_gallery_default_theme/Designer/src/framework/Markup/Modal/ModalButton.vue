<template>
    <jinya-button :label="label" :is-primary="isPrimary" :is-secondary="isSecondary" :is-danger="isDanger"
                  :is-success="isSuccess" :is-inverse="false" :is-disabled="isDisabled" @click="click"/>
</template>

<script>
  import JinyaButton from '@/framework/Markup/Button';

  export default {
    name: 'jinya-modal-button',
    components: { JinyaButton },
    methods: {
      click() {
        this.$emit('click');

        if (this.closesModal) {
          this.$parent.$emit('close');
        }
      },
    },
    props: {
      closesModal: {
        type: Boolean,
        default() {
          return false;
        },
      },
      label: {
        type: String,
        required: true,
      },
      isPrimary: Boolean,
      isSecondary: Boolean,
      isDanger: Boolean,
      isSuccess: Boolean,
      isDisabled: Boolean,
    },
  };
</script>

<style scoped>

</style>
