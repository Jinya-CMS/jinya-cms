<template>
    <button :class="`jinya-gallery-designer__button--${type}`" class="jinya-gallery-designer__button"
            @click="$emit('click')"></button>
</template>

<script>
  export default {
    name: "jinya-gallery-designer-button",
    props: {
      type: {
        type: String,
        required: true
      }
    }
  }
</script>

<style lang="scss">
    .is--horizontal {
        .jinya-gallery-designer__button {
            height: 40em;

            &.jinya-gallery-designer__button--add {
                width: 10em;
            }

            &.jinya-gallery-designer__button--edit {
                grid-column: 1;
                grid-row: 1;
            }
        }
    }

    .is--vertical {
        .jinya-gallery-designer__button {
            &.jinya-gallery-designer__button--add {
                height: 10em;
            }

            &.jinya-gallery-designer__button--edit {
                height: auto;
                width: 100%;
                grid-row: 2;
                grid-column: 1;
            }
        }
    }

    .jinya-gallery-designer__button {
        z-index: 0;
        height: 100%;
        width: 100%;
        background: $gray-200;
        position: relative;
        cursor: pointer;
        font-weight: bold;
        font-size: 100%;
        border: none;

        &.jinya-gallery-designer__button--add {
            transition: transform 0.3s;

            &:hover {
                background: $gray-300;
            }

            &::before {
                //noinspection CssNoGenericFontName
                font-family: "Material Design Icons";
                content: "\f415";
                font-size: 3em;
                color: $primary;
                top: 50%;
                position: absolute;
                left: 50%;
                transform: translate(-50%, -50%);
            }
        }

        &.jinya-gallery-designer__button--edit {
            opacity: 0;
            transition: opacity 0.3s;

            &:hover {
                opacity: 0.8;
            }

            &::before {
                //noinspection CssNoGenericFontName
                font-family: "Material Design Icons";
                content: "\f3eb";
                font-size: 3em;
                color: $primary;
                top: 50%;
                position: absolute;
                left: 50%;
                transform: translate(-50%, -50%);
            }
        }
    }
</style>