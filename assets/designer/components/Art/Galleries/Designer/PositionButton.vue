<template>
    <button :class="classes" @click="$emit('click')"
            class="jinya-gallery-designer__button-position"></button>
</template>

<script>
  export default {
    name: 'jinya-gallery-designer-position-button',
    props: {
      increase: Boolean,
      decrease: Boolean,
    },
    computed: {
      classes() {
        return {
          'jinya-gallery-designer__button-position--increase': this.increase,
          'jinya-gallery-designer__button-position--decrease': this.decrease,
        };
      },
    },
  };
</script>

<style lang="scss">
    .jinya-gallery-designer__button-position {
        cursor: pointer;
        background: $primary;
        color: $primary-lighter;
        border: none;
        padding: 0;
        margin: 0;
        transition: color 0.3s, background 0.3s;
        //noinspection CssNoGenericFontName
        font-family: "Material Design Icons";
        font-size: 100%;

        &::before {
            font-size: 1.6em;
        }

        &:hover {
            background: $primary-lighter;
            color: $primary;
        }

        .is--horizontal & {
            width: 10em;

            &.jinya-gallery-designer__button-position--decrease {
                grid-row: 2;
                grid-column: 1;
                justify-self: start;

                &::before {
                    content: '\f04e';
                }
            }

            &.jinya-gallery-designer__button-position--increase {
                grid-row: 2;
                grid-column: 1;
                justify-self: end;

                &::before {
                    content: '\f055';
                }
            }
        }

        .is--vertical & {
            &.jinya-gallery-designer__button-position--decrease {
                grid-row: 1;

                &::before {
                    content: '\f05e';
                }
            }

            &.jinya-gallery-designer__button-position--increase {
                grid-row: 3;

                &::before {
                    content: '\f046';
                }
            }
        }

        .is--masonry & {
            &.jinya-gallery-designer__button-position--decrease {
                grid-row: 1;
                grid-column: 1;
                justify-self: start;

                &::before {
                    content: '\f04e';
                }
            }

            &.jinya-gallery-designer__button-position--increase {
                grid-row: 1;
                grid-column: 3;
                justify-self: end;

                &::before {
                    content: '\f055';
                }
            }
        }
    }
</style>
