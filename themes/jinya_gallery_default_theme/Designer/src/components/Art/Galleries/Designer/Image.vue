<template>
    <img :src="src" class="jinya-gallery-designer__image">
</template>

<script>
  export default {
    name: "jinya-gallery-designer-image",
    props: {
      src: {
        type: String,
        required: true
      }
    }
  }
</script>

<style lang="scss">
    .jinya-gallery-designer__image {
        .jinya-gallery-designer.is--vertical & {
            height: auto;
            width: 100%;
            grid-row: 2;
            grid-column: 1;
        }

        .jinya-gallery-designer.is--horizontal & {
            height: 40em;
            width: auto;
            grid-column: 1;
            grid-row: 1;
        }
    }
</style>