<template>
    <jinya-modal :loading="loading" @close="$emit('close')" title="static.pages.segment.editor.view.selection.title">
        <jinya-choice :choices="segmentTypes" :selected="{value: selectedType}" @selected="selectedTypeChanged"
                      label="static.pages.segment.editor.view.selection.type"/>
        <div class="jinya-page-editor-add__item">
            <jinya-choice :choices="items" :label="`static.pages.segment.details.action.${selectedType}`"
                          @selected="(value) => this.selectedItem = value.value" v-if="selectedType !== 'html'"/>
            <jinya-choice :choices="actions" :selected="{value: 'none'}"
                          @selected="(value) => this.action = value.value"
                          label="static.pages.segment.details.action.action"
                          v-if="selectedType === 'file'"/>
            <monaco-editor :options="{height: 250}" class="jinya-database-tool__editor" language="javascript"
                           v-if="selectedType === 'file' && action === 'script'" v-model="script"/>
            <jinya-input label="static.pages.segment.details.action.target"
                         v-if="selectedType === 'file' && action === 'link'" v-model="target"/>
            <jinya-tiny-mce height="250px" v-if="selectedType === 'html'" v-model="html"/>
        </div>
        <jinya-modal-button :closes-modal="true" :is-disabled="loading" :is-secondary="true"
                            label="static.pages.segment.editor.view.selection.cancel" slot="buttons-left"/>
        <jinya-modal-button :is-disabled="loading" :is-success="true" @click="save"
                            label="static.pages.segment.editor.view.selection.save" slot="buttons-right"/>
    </jinya-modal>
</template>

<script>
  import MonacoEditor from 'vue-monaco';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import Translator from '@/framework/i18n/Translator';
  import JinyaTinyMce from '@/framework/Markup/Form/TinyMce';
  import JinyaInput from '@/framework/Markup/Form/Input';
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
    props: {
      position: {
        type: Number,
        required: true,
      },
    },
    computed: {
      segmentTypes() {
        return [
          { value: 'html', text: Translator.message('static.pages.segment.editor.view.selection.html') },
          { value: 'file', text: Translator.message('static.pages.segment.editor.view.selection.file') },
          { value: 'gallery', text: Translator.message('static.pages.segment.editor.view.selection.gallery') },
        ];
      },
      actions() {
        return [
          { value: 'none', text: Translator.message('static.pages.segment.details.action.types.none') },
          { value: 'link', text: Translator.message('static.pages.segment.details.action.types.link') },
          { value: 'script', text: Translator.message('static.pages.segment.details.action.types.script') },
        ];
      },
    },
    data() {
      return {
        html: '',
        slug: '',
        action: 'none',
        target: '',
        script: '',
        selectedType: 'html',
        selectedItem: null,
        items: [],
        loading: false,
      };
    },
    methods: {
      async selectedTypeChanged(selection) {
        this.selectedType = selection.value;
        if (this.selectedType !== 'html') {
          let requestUrl = '';
          if (selection.value === 'gallery') {
            requestUrl = '/api/media/gallery';
          } else if (selection.value === 'file') {
            requestUrl = '/api/media/file';
          }

          const response = await JinyaRequest.get(requestUrl);
          this.items = response.items.map((item) => ({ text: item.name, value: item.slug || item.id }));
          if (this.items.length > 0) {
            this.selectedItem = this.items[0].value;
          }
        }
      },
      async save() {
        this.loading = true;
        const data = {
          position: this.position,
        };

        if (this.selectedType === 'gallery') {
          data.gallery = this.selectedItem;
        } else if (this.selectedType === 'file') {
          data.file = this.selectedItem;
          data.action = this.action;

          if (data.action === 'script') {
            data.script = this.script;
          } else if (data.action === 'link') {
            data.target = this.target;
          }
        } else if (this.selectedType === 'html') {
          data.html = this.html;
        }

        const segment = await JinyaRequest.post(`/api/segment_page/${this.$route.params.slug}/segment`, data);
        this.$emit('save', segment);
        this.loading = false;
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

    .jinya-database-tool__editor {
        height: 300px;
        width: 500px;
    }
</style>
