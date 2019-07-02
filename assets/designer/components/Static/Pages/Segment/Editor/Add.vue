<template>
  <jinya-modal @close="$emit('close')" title="static.pages.segment.editor.view.selection.title">
    <jinya-choice :choices="segmentTypes" :selected="type" @selected="selectedTypeChanged"
                  label="static.pages.segment.editor.view.selection.type"/>
    <div class="jinya-page-editor-add__item">
      <jinya-choice :choices="actions" :selected="{value: 'none'}" @selected="(value) => this.action = value.value"
                    label="static.pages.segment.details.action.action"
                    v-if="selectedType === 'artwork'"/>
      <monaco-editor :options="{height: 250}" class="jinya-page-editor__details-editor" language="javascript"
                     v-if="selectedType === 'artwork' && action === 'script'" v-model="script"/>
      <jinya-input label="static.pages.segment.details.action.target"
                   v-if="selectedType === 'artwork' && action === 'link'" v-model="target"/>
      <jinya-tiny-mce height="250px" v-if="selectedType === 'html'" v-model="html"/>
      <jinya-choice :choices="items" :label="`static.pages.segment.details.action.${selectedType}`"
                    v-if="selectedType !== 'html' && selectedType !== 'artwork'"/>
    </div>
    <jinya-modal-button :closes-modal="true" :is-secondary="true"
                        label="static.pages.segment.editor.view.selection.cancel" slot="buttons-right"/>
    <jinya-modal-button :closes-modal="true" :is-primary="true" @click="selected"
                        label="static.pages.segment.editor.view.selection.save" slot="buttons-right"/>
  </jinya-modal>
</template>

<script>
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import Translator from '@/framework/i18n/Translator';
  import JinyaTinyMce from '@/framework/Markup/Form/TinyMce';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import MonacoEditor from 'vue-monaco';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';

  export default {
    components: {
      JinyaInput,
      JinyaTinyMce,
      JinyaChoice,
      JinyaModalButton,
      JinyaModal,
      MonacoEditor,
    },
    name: 'jinya-gallery-designer-add-view',
    computed: {
      segmentTypes() {
        return [
          { value: 'artwork', text: Translator.message('static.pages.segment.editor.view.selection.artwork') },
          { value: 'video', text: Translator.message('static.pages.segment.editor.view.selection.video') },
          {
            value: 'youtube_video',
            text: Translator.message('static.pages.segment.editor.view.selection.youtube_video'),
          },
          { value: 'art_gallery', text: Translator.message('static.pages.segment.editor.view.selection.art_gallery') },
          {
            value: 'video_gallery',
            text: Translator.message('static.pages.segment.editor.view.selection.video_gallery'),
          },
          { value: 'form', text: Translator.message('static.pages.segment.editor.view.selection.form') },
          { value: 'html', text: Translator.message('static.pages.segment.editor.view.selection.html') },
        ];
      },
    },
    data() {
      return {
        type: {
          value: 'html',
          text: Translator.message('static.pages.segment.editor.view.selection.html'),
        },
        html: '',
        slug: '',
        action: '',
        selectedType: 'html',
        items: [],
      };
    },
    methods: {
      async selectedTypeChanged(selection) {
        this.selectedType = selection.value;
        if (this.selectedType !== 'html') {
          let requestUrl = '';
          if (selection.value === 'art_gallery') {
            requestUrl = '/api/gallery/art?count=200000000';
          } else if (selection.value === 'video_gallery') {
            requestUrl = '/api/gallery/video?count=200000000';
          } else if (selection.value === 'video') {
            requestUrl = '/api/video/jinya?count=200000000';
          } else if (selection.value === 'youtube_video') {
            requestUrl = '/api/video/youtube?count=200000000';
          } else if (selection.value === 'artwork') {
            requestUrl = '/api/artwork?count=200000000';
          } else if (selection.value === 'form') {
            requestUrl = '/api/form?count=200000000';
          }

          const response = await JinyaRequest.get(requestUrl);
          this.items = response.items.map(item => ({ text: item.name, value: item.slug }));
        }
      },
      selected() {
        this.$emit('selected', this.selectedType);
      },
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-page-editor-add__item {
    max-height: 500px;
    overflow: auto;
  }

  .jinya-page-editor-add__html {
    height: 250px;
  }
</style>
