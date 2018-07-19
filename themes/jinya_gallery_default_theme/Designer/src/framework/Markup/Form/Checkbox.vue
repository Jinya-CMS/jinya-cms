<template>
    <label class="jinya-checkbox">
        <input @change="$emit('input', $event.target.checked)" :checked="value" type="checkbox"
               :disabled="!enable || static">
        {{label|jmessage}}
    </label>
</template>

<script>
  export default {
    name: 'jinya-checkbox',
    props: {
      static: {
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
      this.id = this._uid;
    },
  };
</script>

<style scoped lang="scss">
    .jinya-checkbox {
        font-size: 1rem;
    }
</style>
