<template>
  <jinya-editor>
    <jinya-message :message="message" :state="state" v-if="state">
      <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error' && isStatic">
        <jinya-button label="art.artworks.artwork_form.back" to="Art.Artworks.SavedInJinya.Overview"
                      :is-danger="true"/>
        <jinya-button label="art.artworks.artwork_form.search" to="Art.Artworks.SavedInJinya.Overview"
                      :query="{keyword: $route.params.slug}" :is-secondary="true"/>
      </jinya-message-action-bar>
    </jinya-message>
    <jinya-form v-if="!(hideOnError && state === 'error')" @submit="save" class="jinya-form--artwork" @back="back"
                :enable="enable" :cancel-label="cancelLabel" :save-label="saveLabel">
      <jinya-editor-pane>
        <jinya-editor-preview-image :src="artwork.picture"/>
      </jinya-editor-pane>
      <jinya-editor-pane>
        <jinya-input :is-static="isStatic" :enable="enable" label="art.artworks.artwork_form.name"
                     v-model="artwork.name" @change="nameChanged" :required="true"
                     :validation-message="'art.artworks.artwork_form.name.empty'|jvalidator"/>
        <jinya-input :is-static="isStatic" :enable="enable" label="art.artworks.artwork_form.slug"
                     v-model="artwork.slug" @change="slugChanged" :required="true"
                     :validation-message="'art.artworks.artwork_form.slug.empty'|jvalidator"/>
        <jinya-file-input v-if="!isStatic" :enable="enable" accept="image/*" @picked="picturePicked"
                          label="art.artworks.artwork_form.artwork" :required="true"
                          :validation-message="'art.artworks.artwork_form.artwork.empty'|jvalidator"/>
        <jinya-textarea :is-static="isStatic" :enable="enable" label="art.artworks.artwork_form.description"
                        v-model="artwork.description"/>
      </jinya-editor-pane>
      <template slot="buttons">
        <slot name="buttons"/>
      </template>
    </jinya-form>
  </jinya-editor>
</template>

<script>
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaFileInput from '@/framework/Markup/Form/FileInput';
  import FileUtils from '@/framework/IO/FileUtils';
  import JinyaTextarea from '@/framework/Markup/Form/Textarea';
  import slugify from 'slugify';
  import Routes from '@/router/Routes';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPreviewImage from '@/framework/Markup/Form/EditorPreviewImage';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';

  export default {
    components: {
      JinyaTextarea,
      JinyaFileInput,
      JinyaInput,
      JinyaEditorPreviewImage,
      JinyaEditorPane,
      JinyaForm,
      JinyaButton,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaEditor,
    },
    name: 'jinya-artwork-form',
    props: {
      message: {
        type: String,
        default() {
          return '';
        },
      },
      state: {
        type: String,
        default() {
          return '';
        },
      },
      isStatic: {
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
      hideOnError: {
        type: Boolean,
        default() {
          return false;
        },
      },
      saveLabel: {
        type: String,
        default() {
          return 'art.artworks.artwork_form.save';
        },
      },
      cancelLabel: {
        type: String,
        default() {
          return 'art.artworks.artwork_form.back';
        },
      },
      artwork: {
        type: Object,
        default() {
          return {
            picture: '',
            name: '',
            slug: '',
            description: '',
          };
        },
      },
      slugifyEnabled: {
        type: Boolean,
        default() {
          return true;
        },
      },
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
          description: this.artwork.description,
        };

        this.$emit('save', artwork);
      },
    },
  };
</script>
