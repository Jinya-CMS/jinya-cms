<template>
  <fieldset class="jinya-fieldset">
    <legend class="jinya-fieldset__legend">{{legend}}</legend>
    <slot/>
  </fieldset>
</template>

<script>
  export default {
    name: 'jinya-fieldset',
    props: {
      legend: {
        type: String,
        required: true,
      },
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-fieldset {
    border: none;
    margin: 0 0 1em;
    padding: 0;

    .jinya-fieldset__legend {
      color: $primary;
      font-size: 1.2rem;
      padding: 0;
    }

  }
</style>
