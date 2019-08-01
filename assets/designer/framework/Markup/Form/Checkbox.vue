<template>
    <label class="jinya-checkbox">
        <input :checked="value" :disabled="!enable || isStatic" @change="$emit('input', $event.target.checked)"
               type="checkbox">
        {{label|jmessage}}
    </label>
</template>

<script>
  export default {
    name: 'jinya-checkbox',
    props: {
      isStatic: {
        type: Boolean,
        default() {
          return false;
        },
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
      label: {
        type: String,
        required: true,
      },
      value: {
        type: Boolean,
      },
    },
    data() {
      return {
        id: null,
      };
    },
    mounted() {
      // eslint-disable-next-line no-underscore-dangle
      this.id = this._uid;
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-checkbox {
        font-size: 1rem;
        width: 100%;
    }
</style>
