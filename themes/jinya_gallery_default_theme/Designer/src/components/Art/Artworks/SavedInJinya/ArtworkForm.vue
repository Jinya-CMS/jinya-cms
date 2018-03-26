<template>
    <jinya-form @submit="save" class="jinya-form--artwork" :cancel-label="cancelLabel" :enable="enable"
                :save-label="saveLabel" @back="back">
        <div class="jinya-form--artwork__pane">
            <img class="jinya-form__preview-image" :src="artwork.picture"/>
        </div>
        <div class="jinya-form--artwork__pane">
            <jinya-input :static="static" :enable="enable" label="art.artworks.artwork_form.name" v-model="artwork.name"
                         @change="nameChanged"/>
            <jinya-input :static="static" :enable="enable" label="art.artworks.artwork_form.slug" v-model="artwork.slug"
                         @change="slugChanged"/>
            <jinya-file-input v-if="!static" :enable="enable" accept="image/*" label="art.artworks.artwork_form.artwork"
                              @picked="picturePicked"/>
            <jinya-textarea :static="static" :enable="enable" label="art.artworks.artwork_form.description"
                            v-model="artwork.description"/>
        </div>
    </jinya-form>
</template>

<script>
  import JinyaForm from "../../../Framework/Markup/Form/Form";
  import JinyaInput from "../../../Framework/Markup/Form/Input";
  import JinyaButton from "../../../Framework/Markup/Button";
  import JinyaFileInput from "../../../Framework/Markup/Form/FileInput";
  import FileUtils from "../../../Framework/IO/FileUtils";
  import JinyaTextarea from "../../../Framework/Markup/Form/Textarea";
  import slugify from "slugify";
  import Routes from "../../../../router/Routes";

  export default {
    components: {
      JinyaTextarea,
      JinyaFileInput,
      JinyaButton,
      JinyaInput,
      JinyaForm
    },
    name: "jinya-artwork-form",
    props: {
      static: {
        type: Boolean,
        default() {
          return false;
        }
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        }
      },
      saveLabel: {
        type: String,
        default() {
          return 'art.artworks.artwork_form.save';
        }
      },
      cancelLabel: {
        type: String,
        default() {
          return 'art.artworks.artwork_form.back';
        }
      },
      artwork: {
        type: Object,
        default() {
          return {
            picture: '',
            name: '',
            slug: '',
            description: ''
          };
        }
      },
      slugifyEnabled: {
        type: Boolean,
        default() {
          return true;
        }
      }
    },
    methods: {
      back() {
        this.$router.push(Routes.Art.Artworks.SavedInJinya.Overview);
      },
      async picturePicked(files) {
        const file = files.item(0);

        this.artwork.picture = await FileUtils.getAsDataUrl(file);
        this.artwork.uploadedFile = file;
      },
      nameChanged(value) {
        if (this.slugifyEnabled) {
          this.artwork.slug = slugify(value);
        }
      },
      slugChanged(value) {
        this.slugifyEnabled = false;
        this.artwork.slug = slugify(value);
      },
      save() {
        const artwork = {
          name: this.artwork.name,
          slug: this.artwork.slug,
          picture: this.artwork.uploadedFile,
          description: this.artwork.description
        };

        this.$emit('save', artwork)
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-form--artwork {
        padding-top: 2em;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;

        .jinya-form--artwork__pane {
            width: 50%;
            padding: 0.5em;

            &:first-of-type {
                padding-left: 0;
            }

            &:last-of-type {
                padding-right: 0;
            }
        }
    }

    .jinya-form__preview-image {
        width: 100%;
        max-height: 35em;
        object-fit: scale-down;
    }
</style>