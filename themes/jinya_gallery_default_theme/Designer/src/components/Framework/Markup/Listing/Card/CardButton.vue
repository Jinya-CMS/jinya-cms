<template>
    <button class="jinya-card-button" :class="`jinya-card-button--${type}`" @click="$emit('click')">
        <span v-if="icon" class="mdi" :class="`mdi-${icon}`"></span>
        <span v-if="text">{{text|jmessage}}</span>
    </button>
</template>

<script>
  export default {
    name: "jinya-card-button",
    props: {
      type: {
        type: String,
        validate(input) {
          return ['details', 'edit', 'delete'].includes(input.toString().toLowerCase());
        }
      },
      icon: {
        type: String,
        validate(input) {
          return this.text || input;
        }
      },
      text: {
        type: String,
        validate(input) {
          return this.icon || input;
        }
      }
    }
  }
</script>

<style scoped lang="scss">
    @include card-footer-button('jinya-card-button');
</style>