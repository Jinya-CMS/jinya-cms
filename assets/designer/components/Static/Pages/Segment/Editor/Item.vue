<template>
    <div class="jinya-page-editor__item">
        <hr class="jinya-segment-page__divider"/>
        <span class="jinya-page-item__heading">{{type}}</span>
        <template v-if="segment.html">
            <div v-html="segment.html" v-if="!editing"></div>
            <jinya-tiny-mce :content="html" v-else v-model="html"/>
        </template>
        <div v-else>
            <dl class="jinya-page-editor__artwork-action">
                <dt>{{'static.pages.segment.details.name'|jmessage}}</dt>
                <dd>{{name}}</dd>
            </dl>
            <template v-if="segment.type === 'artwork' || segment.artwork">
                <template v-if="!editing">
                    <dl class="jinya-page-editor__artwork-action">
                        <dt>{{'static.pages.segment.details.action.action'|jmessage}}</dt>
                        <dd>
                            {{`static.pages.segment.details.action.types.${segment.action}`|jmessage}}
                        </dd>
                        <template v-if="segment.action === 'link'">
                            <dt>{{'static.pages.segment.details.action.target'|jmessage}}</dt>
                            <dd>{{segment.target}}</dd>
                        </template>
                    </dl>
                </template>
                <template v-else>
                    <jinya-input label="static.pages.segment.details.action.target" v-model="target"/>
                    <jinya-choice :choices="actions" :selected="{value: action}"
                                  @selected="(value) => segment.action = value.value"
                                  label="static.pages.segment.details.action.action"/>
                </template>
                <template v-if="segment.action === 'script'">
                    <b class="jinya-page-editor__line">{{'static.pages.segment.details.action.script'|jmessage}}</b>
                    <monaco-editor :options="{readOnly: !editing, height: 400}"
                                   class="jinya-page-editor__details-editor"
                                   language="javascript" v-model="script"/>
                </template>
            </template>
        </div>
        <div class="jinya-page-editor__details" v-if="editing">
            <jinya-button :is-inverse="true" :is-secondary="true" @click="resetEdit"
                          class="jinya-page-editor__cancel-edit" label="static.pages.segment.details.cancel"/>
            <jinya-button :is-inverse="true" :is-primary="true" @click="saveEdit"
                          class="jinya-page-editor__save-details" label="static.pages.segment.details.save"/>
        </div>
        <div class="jinya-page-editor__button-bar" v-if="!editing">
            <jinya-icon-button :is-inverse="false" :is-primary="true" @click="edit" class="jinya-icon-button--editor"
                               icon="pencil"/>
            <jinya-icon-button :is-danger="true" :is-inverse="false" @click="$emit('delete', segment, index)"
                               class="jinya-icon-button--editor" icon="delete"/>
            <jinya-icon-button :is-inverse="false" :is-secondary="true"
                               @click="$emit('move', segment, index, index - 1)"
                               @wheel.native="$emit('scroll')" class="jinya-icon-button--editor" icon="arrow-up-thick"
                               v-if="index > 0"/>
            <jinya-icon-button :is-inverse="false" :is-secondary="true"
                               @click="$emit('move', segment, index, index + 1)"
                               @wheel.native="$emit('scroll')" class="jinya-icon-button--editor" icon="arrow-down-thick"
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
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';

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
      segmentCount: {
        type: Number,
      },
    },
    mounted() {
      this.editing = false;
    },
    watch: {
      segment(oldData, newData) {
        this.script = newData.script;
        this.target = newData.target;
        this.action = newData.action;
        this.html = newData.html;
      },
    },
    methods: {
      add() {
        this.$emit('add', this.index + 1);
      },
      resetEdit() {
        this.script = this.segment.script;
        this.target = this.segment.target;
        this.action = this.segment.action;
        this.html = this.segment.html;
        this.editing = false;
      },
      async edit() {
        this.editing = true;

        if (this.segment.html) {
          let requestUrl = '';
          if (this.segment.artGallery) {
            requestUrl = '/api/gallery/art?count=200000000';
          } else if (this.segment.videoGallery) {
            requestUrl = '/api/gallery/video?count=200000000';
          } else if (this.segment.video) {
            requestUrl = '/api/video/jinya?count=200000000';
          } else if (this.segment.youtubeVideo) {
            requestUrl = '/api/video/youtube?count=200000000';
          } else if (this.segment.artwork) {
            requestUrl = '/api/artwork?count=200000000';
          }

          const response = await JinyaRequest.get(requestUrl);
          this.items = response.items.map(item => ({ text: item.name, value: item.slug }));
          if (this.items.length > 0) {
            this.selectedItem = this.items[0].value;
          }
        }
      },
      async saveEdit() {
        if (this.segment.html || this.segment.artwork) {
          const data = {};
          if (this.segment.html) {
            data.html = this.html;
          } else if (this.segment.artwork) {
            data.action = this.action;
            if (data.action === 'link') {
              data.target = this.target;
              data.script = null;
            }
            if (data.action === 'script') {
              data.script = this.script;
              data.target = null;
            }
            if (data.action === 'none') {
              data.script = null;
              data.target = null;
            }
          }

          await JinyaRequest.put(`/api/segment_page/${this.$route.params.slug}/segment/${this.segment.id}`, data);
          this.editing = false;
          data.artwork = this.segment.artwork.slug;

          this.$emit('edit-saved', this.index, data);
        }
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
      name() {
        if (this.segment.artwork) {
          return this.segment.artwork.name;
        }
        if (this.segment.video) {
          return this.segment.video.name;
        }
        if (this.segment.artGallery) {
          return this.segment.artGallery.name;
        }
        if (this.segment.videoGallery) {
          return this.segment.videoGallery.name;
        }
        if (this.segment.youtubeVideo) {
          return this.segment.youtubeVideo.name;
        }

        return '';
      },
      type() {
        if (this.segment.artwork) {
          return Translator.message('static.pages.segment.editor.view.selection.artwork');
        }
        if (this.segment.video) {
          return Translator.message('static.pages.segment.editor.view.selection.video');
        }
        if (this.segment.artGallery) {
          return Translator.message('static.pages.segment.editor.view.selection.art_gallery');
        }
        if (this.segment.videoGallery) {
          return Translator.message('static.pages.segment.editor.view.selection.video_gallery');
        }
        if (this.segment.youtubeVideo) {
          return Translator.message('static.pages.segment.editor.view.selection.youtube_video');
        }
        if (this.segment.html) {
          return Translator.message('static.pages.segment.editor.view.selection.html');
        }

        return '';
      },
    },
    data() {
      return {
        editing: false,
        script: this.segment.script,
        target: this.segment.target,
        action: this.segment.action,
        html: this.segment.html,
      };
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page-editor__button-bar {
        display: flex;
        justify-content: space-evenly;
        margin-top: 1rem;
    }

    .jinya-page-editor__cancel-edit {
        margin-right: 1rem;
    }

    .jinya-icon-button--editor {
        flex: 1 1 25%;
        margin-left: 0.5rem;
        margin-right: 0.5rem;

        &:first-of-type {
            margin-left: 0;
        }

        &:last-of-type {
            margin-right: 0;
        }
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

    .jinya-page-item__heading {
        font-size: 1.25rem;
        font-weight: lighter;
        margin-bottom: 0.25rem;
    }

    .jinya-page-editor__line {
        display: block;
    }

    .jinya-page-editor__artwork-action {
        display: flex;
        flex-flow: row wrap;
        margin: 0;

        dt {
            font-weight: bold;
            min-width: 15%;
            flex: 0 0 15%;
            margin-bottom: 0.25rem;
        }

        dd {
            min-width: 85%;
            flex: 0 0 85%;
            margin: 0;
            margin-bottom: 0.25rem;
        }
    }
</style>
