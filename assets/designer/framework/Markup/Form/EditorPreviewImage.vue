<template>
  <img :src="imgSrc" class="jinya-form__preview-image"/>
</template>

<script>
  export default {
    name: 'jinya-editor-preview-image',
    props: {
      src: {
        type: String,
        default() {
          return '';
        },
      },
    },
    watch: {
      src(newVal) {
        this.imgSrc = newVal;
      },
    },
    data() {
      return {
        imgSrc: this.src,
      };
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-form__preview-image {
    width: 100%;
    max-height: 35em;
    object-fit: scale-down;
  }
</style>
