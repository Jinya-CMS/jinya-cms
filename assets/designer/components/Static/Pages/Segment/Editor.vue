<template>
    <jinya-loader :loading="loading" v-if="loading"/>
    <jinya-editor v-else>
        <jinya-editor-pane class="jinya-page-editor__preview">
            <jinya-page-editor-preview-pane :segments="segments"/>
        </jinya-editor-pane>
        <jinya-editor-pane @wheel="scroll" class="jinya-page-editor" ref="editor">
            <jinya-message :message="message" :state="state" v-if="state"/>
            <jinya-icon-button :is-primary="true" @click="add(-1)" class="jinya-icon-button--add" icon="plus"/>
            <template v-for="(segment, index) in segments">
                <jinya-page-editor-item :index="index" :key="segment.id" :segment="segment"
                                        :segment-count="segments.length" @add="add" @delete="showDeleteModal"
                                        @edit-saved="saveEdit" @move="move" @scroll="scroll" @wheel.native="scroll"/>
            </template>
        </jinya-editor-pane>
        <jinya-page-editor-add-view :position="currentPosition" @close="addModal.show = false" @save="saveAdd"
                                    v-if="addModal.show"/>
        <jinya-modal :loading="deleteModal.loading" @close="closeDeleteModal"
                     title="static.pages.segment.details.segment.delete.title" v-if="deleteModal.show">
            <jinya-message :message="deleteModal.error" slot="message" state="error"
                           v-if="deleteModal.error && !deleteModal.loading"/>
            {{deleteContent}}
            <jinya-modal-button :closes-modal="true" :is-disabled="this.deleteModal.loading" :is-secondary="true"
                                label="static.pages.segment.details.segment.delete.no" slot="buttons-left"/>
            <jinya-modal-button :is-danger="true" :is-disabled="this.deleteModal.loading" @click="deleteSegment"
                                label="static.pages.segment.details.segment.delete.yes" slot="buttons-right"/>
        </jinya-modal>
    </jinya-editor>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaPageEditorItem from '@/components/Static/Pages/Segment/Editor/Item';
  import JinyaPageEditorAddView from '@/components/Static/Pages/Segment/Editor/Add';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaIconButton from '@/framework/Markup/IconButton';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import Translator from '@/framework/i18n/Translator';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaPageEditorPreviewPane from '@/components/Static/Pages/Segment/Editor/PreviewPane';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';

  export default {
    components: {
      JinyaModalButton,
      JinyaModal,
      JinyaPageEditorPreviewPane,
      JinyaEditorPane,
      JinyaEditor,
      JinyaLoader,
      JinyaIconButton,
      JinyaMessage,
      JinyaPageEditorAddView,
      JinyaPageEditorItem,
    },
    name: 'JinyaSegmentPageEditor',
    computed: {
      deleteContent() {
        const segment = this.selectedSegment;

        if (segment.html) {
          return Translator.message('static.pages.segment.details.segment.delete.content_html', segment);
        }
        if (segment.artwork) {
          return Translator.message('static.pages.segment.details.segment.delete.content', segment.artwork);
        }
        if (segment.artGallery) {
          return Translator.message('static.pages.segment.details.segment.delete.content', segment.artGallery);
        }
        if (segment.video) {
          return Translator.message('static.pages.segment.details.segment.delete.content', segment.video);
        }
        if (segment.youtubeVideo) {
          return Translator.message('static.pages.segment.details.segment.delete.content', segment.youtubeVideo);
        }
        if (segment.videoGallery) {
          return Translator.message('static.pages.segment.details.segment.delete.content', segment.videoGallery);
        }
        if (segment.gallery) {
          return Translator.message('static.pages.segment.details.segment.delete.content', segment.gallery);
        }
        if (segment.file) {
          return Translator.message('static.pages.segment.details.segment.delete.content', segment.file);
        }

        return '';
      },
    },
    methods: {
      scroll($event) {
        if (!$event.deltaX && !this.addModal.show) {
          this.$refs.editor.scrollBy({
            behavior: 'auto',
            left: $event.deltaY > 0 ? 100 : -100,
          });
        }
      },
      showDeleteModal(segment, index) {
        this.selectedSegment = segment;
        this.currentPosition = index;
        this.deleteModal.show = true;
      },
      closeDeleteModal() {
        this.deleteModal.show = false;
      },
      async move(segment, oldPosition, newPosition) {
        await JinyaRequest.put(`/api/segment_page/${this.$route.params.slug}/segment/${segment.id}`, {
          position: newPosition,
        });

        if (oldPosition < newPosition) {
          this.segments.splice(newPosition + 1, 0, segment);
          this.segments.splice(oldPosition, 1);
        } else {
          this.segments.splice(newPosition, 0, segment);
          this.segments.splice(oldPosition + 1, 1);
        }
      },
      async saveAdd(segment) {
        this.segments.splice(this.currentPosition + 1, 0, segment);
        this.addModal.show = false;
      },
      async deleteSegment() {
        this.deleteModal.loading = true;
        try {
          await JinyaRequest.delete(`/api/segment_page/${this.$route.params.slug}/segment/${this.selectedSegment.id}`);
          this.segments.splice(this.currentPosition, 1);

          this.deleteModal.show = false;
        } catch (e) {
          this.deleteModal.message = Translator.validator('static.pages.segment.editor.delete_failed');
        }

        this.deleteModal.loading = false;
      },
      async saveEdit(position, segment) {
        this.segments[position].html = segment.html;
        this.segments[position].action = segment.action;
        this.segments[position].script = segment.script;
        this.segments[position].target = segment.target;
      },
      add(position) {
        this.addModal.show = true;
        this.currentPosition = position;
      },
    },
    async mounted() {
      this.loading = true;
      try {
        this.segmentPage = await JinyaRequest.get(`/api/segment_page/${this.$route.params.slug}`);
        this.segments = await JinyaRequest.get(`/api/segment_page/${this.$route.params.slug}/segment`);
        DOMUtils.changeTitle(Translator.message('static.pages.segment.editor.title', this.segmentPage));
      } catch (error) {
        this.message = Translator.message('static.pages.segment.editor.loading_failed');
        this.state = 'error';
      }
      this.loading = false;
    },
    data() {
      return {
        segmentPage: [],
        segments: [],
        selectedSegment: null,
        state: '',
        message: '',
        currentPosition: -1,
        loading: false,
        addModal: {
          show: false,
        },
        deleteModal: {
          loading: false,
          message: '',
          show: false,
        },
      };
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page-editor {
        padding-top: 2rem;
    }

    .jinya-message--editor {
        margin-right: -12.5%;
        margin-left: -12.5%;
        width: 125%;
        padding-top: 1em;
    }

    .jinya-icon-button--add {
        min-width: 100%;
        flex: 0 0 100%;
        font-size: 3rem;
    }

    .jinya-page-editor__preview {
        overflow-x: hidden;
    }
</style>
