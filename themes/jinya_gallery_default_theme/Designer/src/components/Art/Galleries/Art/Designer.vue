<template>
    <div class="jinya-gallery-designer" :class="`is--${gallery.orientation}`">
        <jinya-loader :loading="loading"/>
        <button class="jinya-gallery-designer__button jinya-gallery-designer__button--add" v-if="!loading"></button>
        <template v-if="!loading" v-for="(position, index) in artworks">
            <div class="jinya-gallery-designer__item">
                <img :src="position.artwork.picture" class="jinya-gallery-designer__image">
                <button class="jinya-gallery-designer__button jinya-gallery-designer__button--edit"></button>
                <button v-if="index > 0"
                        class="jinya-gallery-designer__button-position jinya-gallery-designer__button-position--decrease"></button>
                <button v-if="index + 1 < artworks.length"
                        class="jinya-gallery-designer__button-position jinya-gallery-designer__button-position--increase"></button>
            </div>
            <button class="jinya-gallery-designer__button jinya-gallery-designer__button--add"></button>
        </template>
    </div>
</template>

<script>
  import JinyaRequest from "@/components/Framework/Ajax/JinyaRequest";
  import JinyaLoader from "@/components/Framework/Markup/Loader";
  import DOMUtils from "@/components/Framework/Utils/DOMUtils";
  import Translator from "@/components/Framework/i18n/Translator";
  import JinyaButton from "@/components/Framework/Markup/Button";

  export default {
    components: {
      JinyaButton,
      JinyaLoader
    },
    name: "designer",
    async mounted() {
      this.loading = true;
      try {
        const gallery = await JinyaRequest.get(`/api/gallery/${this.$route.params.slug}`);
        this.gallery = gallery.item;
        this.artworks = await JinyaRequest.get(`/api/gallery/${this.$route.params.slug}/artwork`);
        DOMUtils.changeTitle(Translator.message('art.galleries.designer.title', this.gallery));
      } catch (error) {
      }
      this.loading = false;
    },
    data() {
      return {
        gallery: {
          name: '',
          orientation: '',
          background: ''
        },
        artworks: [],
        loading: false
      };
    }
  }
</script>

<style scoped lang="scss">

    .jinya-gallery-designer {
        height: 100%;
        width: 100%;
        display: grid;
        grid-gap: 1em;

        &.is--horizontal {
            $image-height: 40em;
            padding-bottom: 10em;
            grid-template-columns: repeat(auto-fill, minmax(10em, 100%));
            grid-auto-flow: column;
            padding-top: 1em;
            overflow-x: auto;
            margin-right: -10%;
            margin-left: -10%;
            width: 120%;

            .jinya-gallery-designer__button {
                height: $image-height;

                &.jinya-gallery-designer__button--add {
                    width: 10em;
                }
            }

            .jinya-gallery-designer__item {
                .jinya-gallery-designer__image {
                    height: $image-height;
                    width: auto;
                    grid-column: 1;
                    grid-row: 1;
                }

                .jinya-gallery-designer__button {
                    &.jinya-gallery-designer__button--edit {
                        grid-column: 1;
                        grid-row: 1;
                    }
                }

                .jinya-gallery-designer__button-position {
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
            }
        }

        &.is--vertical {
            grid-template-rows: repeat(auto-fill, minmax(10em, 100%));
            padding-top: 10em;

            .jinya-gallery-designer__button {
                &.jinya-gallery-designer__button--add {
                    height: 10em;
                }
            }

            .jinya-gallery-designer__item {
                grid-template-rows: auto 1fr auto;

                .jinya-gallery-designer__image,
                .jinya-gallery-designer__button.jinya-gallery-designer__button--edit {
                    height: auto;
                    width: 100%;
                    grid-row: 2;
                    grid-column: 1;
                }

                .jinya-gallery-designer__button-position {
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
            }
        }

        &:hover {
            + .jinya-gallery-designer__button {
                opacity: 1;
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
                transform: scale(1, 1);

                &:hover {
                    transform: scale(0.9, 0.9);
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

        .jinya-gallery-designer__item {
            display: grid;
        }

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
        }
    }

</style>