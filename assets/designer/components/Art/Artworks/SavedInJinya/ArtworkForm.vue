<template>
  <jinya-editor>
    <jinya-message :message="message" :state="state" v-if="state">
      <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error' && isStatic">
        <jinya-button :is-danger="true" label="art.artworks.artwork_form.back"
                      to="Art.Artworks.SavedInJinya.Overview"/>
        <jinya-button :is-secondary="true" :query="{keyword: $route.params.slug}"
                      label="art.artworks.artwork_form.search" to="Art.Artworks.SavedInJinya.Overview"/>
      </jinya-message-action-bar>
    </jinya-message>
    <jinya-form :cancel-label="cancelLabel" :enable="enable" :save-label="saveLabel" @back="back"
                @submit="save" class="jinya-form--artwork" v-if="!(hideOnError && state === 'error')">
      <jinya-editor-pane>
        <jinya-editor-preview-image :src="artwork.picture"/>
      </jinya-editor-pane>
      <jinya-editor-pane>
        <jinya-input :enable="enable" :is-static="isStatic" :required="true"
                     :validation-message="'art.artworks.artwork_form.name.empty'|jvalidator"
                     label="art.artworks.artwork_form.name" v-model="artwork.name"/>
        <jinya-file-input :enable="enable" :required="true"
                          :validation-message="'art.artworks.artwork_form.artwork.empty'|jvalidator"
                          @picked="picturePicked"
                          accept="image/*" label="art.artworks.artwork_form.artwork" v-if="!isStatic"/>
        <jinya-choice :choices="types" :enforceSelect="true" @selected="value=> artwork.type = value.value"
                      label="art.artworks.artwork_form.convert.label" v-if="!isStatic"/>
        <jinya-textarea :enable="enable" :is-static="isStatic" label="art.artworks.artwork_form.description"
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
  import Routes from '@/router/Routes';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPreviewImage from '@/framework/Markup/Form/EditorPreviewImage';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import Translator from '@/framework/i18n/Translator';

  export default {
    components: {
      JinyaChoice,
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
            type: 0,
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
    data() {
      return {
        types: [],
      };
    },
    async created() {
      const supportedTypes = await JinyaRequest.get('/api/media/conversion/type');

      this.types = [
        {
          value: 0,
          text: Translator.message('art.artworks.artwork_form.convert.not_convert'),
        },
      ].concat(supportedTypes.map(item => ({
        value: Object.keys(item)[0],
        text: Object.values(item)[0],
      })));
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
      save() {
        const artwork = {
          name: this.artwork.name,
          picture: this.artwork.uploadedFile,
          description: this.artwork.description,
          type: this.artwork.type,
        };

        this.$emit('save', artwork);
      },
    },
  };
</script>
