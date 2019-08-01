<template>
    <button :class="`jinya-gallery-designer__button--${type}`" @click="$emit('click')"
            class="jinya-gallery-designer__button"></button>
</template>

<script>
  export default {
    name: 'jinya-gallery-designer-button',
    props: {
      type: {
        type: String,
        required: true,
      },
    },
  };
</script>

<style lang="scss">
    .jinya-gallery-designer__button {
        height: 100%;
        width: 100%;
        background: $gray-200;
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
                left: 50%;
                transform: translate(-50%, -50%);
            }
        }

        &.jinya-gallery-designer__button--edit {
            background: rgba(255, 255, 255, 0);
            transition: background 0.3s;

            &:hover {
                background: rgba(255, 255, 255, 0.8);

                &::before {
                    color: $primary;
                }
            }

            &::before {
                //noinspection CssNoGenericFontName
                font-family: "Material Design Icons";
                content: "\f3eb";
                font-size: 3em;
                color: transparent;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
        }

        .is--horizontal & {
            height: 40em;

            &.jinya-gallery-designer__button--add {
                width: 10em;
            }

            &.jinya-gallery-designer__button--edit {
                grid-column: 1;
                grid-row: 1;
            }
        }

        .is--vertical & {
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

        .is--masonry & {
            height: 10rem;
            width: auto;

            &.jinya-gallery-designer__button--edit {
                grid-column: 2;
                grid-row: 1;
            }

            &.jinya-gallery-designer__button--add {
                margin-top: 1rem;
                margin-right: 1rem;
            }
        }
    }
</style>
