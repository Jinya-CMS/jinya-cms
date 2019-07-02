<template>
  <div class="jinya-page-editor__item">
    <hr class="jinya-segment-page__divider"/>
    <div v-html="segment.html" v-if="segment.html"></div>
    <div v-else>
      <span class="jinya-page-item__heading">{{segment.name}}</span>
      <div v-if="segment.type === 'artwork' || segment.artwork">
        <span v-if="!editing">
          <b>{{'static.pages.segment.details.action.action'|jmessage}}</b>:
          {{`static.pages.segment.details.action.types.${segment.action}`|jmessage}}
        </span>
        <jinya-choice :choices="actions" :selected="{value: segment.action}"
                      @selected="(value) => segment.action = value.value"
                      label="static.pages.segment.details.action.action" v-else/>
      </div>
      <div v-if="(segment.type === 'artwork' || segment.artwork) && segment.action === 'script'">
        <span>
          <b>{{'static.pages.segment.details.action.script'|jmessage}}</b><br/>
          <monaco-editor :options="{readOnly: !editing, height: 400}" class="jinya-page-editor__details-editor"
                         language="javascript" v-model="segment.script"/>
        </span>
      </div>
      <div v-if="(segment.type === 'artwork' || segment.artwork) && segment.action === 'link'">
        <span class="jinya-page-item__heading" v-if="!editing">
          <b>{{'static.pages.segment.details.action.target'|jmessage}}</b>: {{segment.target}}
        </span>
        <jinya-input label="static.pages.segment.details.action.target" v-else v-model="segment.target"/>
      </div>
      <div v-if="segment.type === 'html'">
        <div v-html="segment.html" v-if="!editing"></div>
        <jinya-tiny-mce v-else v-model="segment.html"/>
      </div>
      <div class="jinya-page-editor__details" v-if="editing">
        <jinya-button :is-inverse="true" :is-primary="true" @click="editing = false"
                      class="jinya-page-editor__save-details" label="static.pages.segment.details.save"/>
      </div>
    </div>
    <div>
      <jinya-icon-button :is-primary="true" @click="editing = true" class="jinya-icon-button--editor"
                         icon="pencil"/>
      <jinya-icon-button :is-danger="true" @click="emit('delete', { id: segment.id, index })"
                         class="jinya-icon-button--editor" icon="delete"/>
      <jinya-icon-button :is-secondary="true"
                         @click="emit('move', {segment, oldPosition: index, newPosition: index - 1})"
                         @wheel.native="scroll" class="jinya-icon-button--editor" icon="arrow-up-thick"
                         v-if="index > 0"/>
      <jinya-icon-button :is-secondary="true"
                         @click="emit('move', {segment, oldPosition: index, newPosition: index + 1})"
                         @wheel.native="scroll" class="jinya-icon-button--editor" icon="arrow-down-thick"
                         v-if="index + 1 < segmentCount"/>
    </div>
    <hr class="jinya-segment-page__divider"/>
    <jinya-icon-button :is-primary="true" @click="add" class="jinya-icon-button--add" icon="plus"/>
  </div>
</template>

<script>
  import MonacoEditor from 'vue-monaco';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaIconButton from '@/framework/Markup/IconButton';
  import Translator from '@/framework/i18n/Translator';
  import JinyaTinyMce from '@/framework/Markup/Form/TinyMce';
  import JinyaButton from '@/framework/Markup/Button';

  export default {
    name: 'jinya-page-editor-item',
    components: {
      JinyaButton,
      JinyaTinyMce,
      JinyaIconButton,
      JinyaInput,
      JinyaChoice,
      MonacoEditor,
    },
    props: {
      segment: {
        required: true,
      },
      index: {
        type: Number,
        required: true,
      },
      readOnly: {
        type: Boolean,
      },
      segmentCount: {
        type: Number,
      },
    },
    mounted() {
      this.editing = !this.readOnly;
    },
    methods: {
      add() {
        this.$emit('add', this.index + 1);
      },
    },
    computed: {
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
        editing: false,
      };
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-icon-button--editor {
    min-width: 25%;
    flex: 1 0 25%;
  }

  .jinya-icon-button--add {
    min-width: 100%;
    flex: 0 0 100%;
    font-size: 3rem;
  }

  .jinya-segment-page__divider {
    border: none;
    border-bottom: 1px solid $secondary;
    margin-top: 2rem;
    margin-bottom: 2rem;
  }

  .jinya-page-editor__details {
    display: flex;
    justify-content: flex-end;
  }

  .jinya-page-editor__item {
    width: 100%;
  }

  .jinya-page-editor__details-editor {
    height: 400px;
  }
</style>
