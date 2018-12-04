<template>
  <jinya-editor>
    <jinya-message :message="message" :state="state" v-if="state">
      <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
        <jinya-button label="art.galleries.gallery_form.back" to="Art.Galleries.Art.Overview"
                      :is-danger="true"/>
        <jinya-button label="art.galleries.gallery_form.search" to="Art.Galleries.Art.Overview"
                      :query="{keyword: $route.params.slug}" :is-secondary="true"/>
      </jinya-message-action-bar>
    </jinya-message>
    <jinya-form v-if="!(hideOnError && state === 'error')" @submit="save" class="jinya-form--gallery" @back="back"
                :enable="enable" :cancel-label="cancelLabel" :save-label="saveLabel">
      <jinya-editor-pane>
        <label>Hintergrund</label>
        <jinya-editor-preview-image :src="gallery.background"/>
      </jinya-editor-pane>
      <jinya-editor-pane>
        <jinya-input :is-static="isStatic" :enable="enable" label="art.galleries.gallery_form.name"
                     v-model="gallery.name" @change="nameChanged" :required="true"
                     :validation-message="'art.galleries.gallery_form.name.empty'|jvalidator"/>
        <jinya-input :is-static="isStatic" :enable="enable" label="art.galleries.gallery_form.slug"
                     v-model="gallery.slug" @change="slugChanged" :required="true"
                     :validation-message="'art.galleries.gallery_form.slug.empty'|jvalidator"/>
        <jinya-choice :is-static="isStatic" label="art.galleries.gallery_form.orientation"
                      :choices="orientations" :enable="enable" :selected="gallery.orientation"
                      @selected="(value) => gallery.orientation = value"/>
        <jinya-file-input v-if="!isStatic" :enable="enable" accept="image/*" @picked="backgroundPicked"
                          label="art.galleries.gallery_form.background"/>
        <jinya-textarea :is-static="isStatic" :enable="enable" label="art.galleries.gallery_form.description"
                        v-model="gallery.description"/>
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
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPreviewImage from '@/framework/Markup/Form/EditorPreviewImage';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import Translator from '@/framework/i18n/Translator';

  export default {
    components: {
      JinyaChoice,
      JinyaEditorPane,
      JinyaEditorPreviewImage,
      JinyaEditor,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaTextarea,
      JinyaFileInput,
      JinyaButton,
      JinyaInput,
      JinyaForm,
    },
    name: 'jinya-gallery-form',
    props: {
      backTarget: {
        type: String,
        required: true,
      },
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
          return 'art.galleries.gallery_form.save';
        },
      },
      cancelLabel: {
        type: String,
        default() {
          return 'art.galleries.gallery_form.back';
        },
      },
      gallery: {
        type: Object,
        default() {
          return {
            background: '',
            name: '',
            slug: '',
            description: '',
            orientation: {
              value: 'horizontal',
              text: '',
            },
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
    computed: {
      orientations() {
        return [
          {
            value: 'horizontal',
            text: Translator.message('art.galleries.gallery_form.orientations.horizontal'),
          },
          {
            value: 'vertical',
            text: Translator.message('art.galleries.gallery_form.orientations.vertical'),
          },
        ];
      },
    },
    watch: {
      gallery(newVal) {
        this.gallery.orientation = {
          value: newVal.orientation,
          text: Translator.message(`art.galleries.gallery_form.orientations.${newVal.orientation}`),
        };
      },
    },
    methods: {
      back() {
        this.$router.push(this.backTarget);
      },
      async backgroundPicked(files) {
        const file = files.item(0);

        this.gallery.background = await FileUtils.getAsDataUrl(file);
        this.gallery.uploadedFile = file;
      },
      nameChanged(value) {
        if (this.slugifyEnabled) {
          this.gallery.slug = slugify(value);
        }
      },
      slugChanged(value) {
        this.slugifyEnabled = false;
        this.gallery.slug = slugify(value);
      },
      save() {
        const gallery = {
          name: this.gallery.name,
          slug: this.gallery.slug,
          background: this.gallery.uploadedFile,
          description: this.gallery.description,
          orientation: this.gallery.orientation.value,
        };

        this.$emit('save', gallery);
      },
    },
  };
</script>
