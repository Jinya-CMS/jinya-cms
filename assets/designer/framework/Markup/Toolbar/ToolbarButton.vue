<template>
    <jinya-button :is-danger="isDanger" :is-disabled="isDisabled" :is-inverse="isInverse" :is-primary="isPrimary"
                  :is-secondary="isSecondary" :is-success="isSuccess" :label="label" @click="$emit('click')"
                  class="jinya-toolbar__button" type="button" v-if="label"/>
    <jinya-icon-button :icon="icon" :is-danger="isDanger" :is-disabled="isDisabled" :is-inverse="isInverse"
                       :is-primary="isPrimary" :is-secondary="isSecondary" :is-success="isSuccess"
                       @click="$emit('click')" class="jinya-toolbar__button" type="button" v-else-if="icon"/>
</template>

<script>
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaIconButton from '@/framework/Markup/IconButton';

  export default {
    name: 'JinyaToolbarButton',
    components: { JinyaIconButton, JinyaButton },
    props: {
      icon: String,
      label: String,
      isPrimary: Boolean,
      isSecondary: Boolean,
      isDanger: Boolean,
      isSuccess: Boolean,
      isDisabled: Boolean,
      isInverse: {
        type: Boolean,
        default() {
          return true;
        },
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-toolbar__button {
        margin-right: 1rem;
    }
</style>
