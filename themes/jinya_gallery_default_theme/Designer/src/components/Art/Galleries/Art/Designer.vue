<template>
    <div class="jinya-gallery-designer" :class="`is--${gallery.orientation}`">
        <jinya-loader :loading="loading"/>
        <div class="jinya-gallery-designer__button jinya-gallery-designer__button--add" v-if="!loading"></div>
        <template v-if="!loading" v-for="position in artworks">
            <div class="jinya-gallery-designer__item">
                <div class="jinya-gallery-designer__content">
                    <img :src="position.artwork.picture" class="jinya-gallery-designer__image">
                    <div class="jinya-gallery-designer__button jinya-gallery-designer__button--edit"></div>
                </div>
                <div class="jinya-gallery-designer__action-bar">
                    <div class="jinya-gallery-designer__button-move--left"></div>
                    <span class="jinya-gallery-designer__image-name">{{position.artwork.name}}</span>
                    <div class="jinya-gallery-designer__button-move--right"></div>
                </div>
            </div>
            <div class="jinya-gallery-designer__button jinya-gallery-designer__button--add"></div>
        </template>
    </div>
</template>

<script>
  import JinyaRequest from "@/components/Framework/Ajax/JinyaRequest";
  import JinyaLoader from "@/components/Framework/Markup/Loader";
  import DOMUtils from "@/components/Framework/Utils/DOMUtils";
  import Translator from "@/components/Framework/i18n/Translator";

  export default {
    components: {JinyaLoader},
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
        display: flex;
        height: 100%;
        width: 100%;

        &.is--horizontal {
            flex-direction: row;
            flex-wrap: nowrap;
            padding-bottom: 10em;

            .jinya-gallery-designer__button {
                &.jinya-gallery-designer__button--add {
                    flex-basis: 10em;
                    width: 10em;
                }
            }
        }
        &.is--vertical {
            flex-direction: column;

            .jinya-gallery-designer__button {
                &.jinya-gallery-designer__button--add {
                    flex-basis: 10em;
                    height: 10em;
                    width: 100%;
                }
            }
        }

        .jinya-gallery-designer__button {
            flex: 0 0 auto;
            height: 100%;
            width: 100%;
            background: $gray-200;
            margin: 1em;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s;
            transform: scale(1, 1);
            font-weight: bold;

            &:hover {
                transform: scale(0.9, 0.9);
                background: $gray-300;
            }

            &.jinya-gallery-designer__button--add {
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
        }
    }

</style>