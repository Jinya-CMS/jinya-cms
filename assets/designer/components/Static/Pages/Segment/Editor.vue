<template>
  <jinya-loader v-if="loading"/>
  <jinya-editor v-else>
    <jinya-editor-pane>
      <div></div>
    </jinya-editor-pane>
    <jinya-editor-pane @wheel="scroll" class="jinya-page-editor" ref="editor">
      <jinya-message :message="message" :state="state" v-if="state"/>
      <jinya-icon-button :is-primary="true" @click="add(-1)" class="jinya-icon-button--add" icon="plus"/>
      <template v-for="(segment, index) in segments">
        <jinya-page-editor-item :index="index" :key="segment.id" :segment="segment" :segment-count="segments.length"
                                @add="add" @delete="deleteArtwork" @move="move" @wheel.native="scroll"/>
      </template>
      <jinya-page-editor-add-view @close="addModal.show = false" @selected="saveAdd" v-if="addModal.show"/>
    </jinya-editor-pane>
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

  export default {
    components: {
      JinyaEditorPane,
      JinyaEditor,
      JinyaLoader,
      JinyaIconButton,
      JinyaMessage,
      JinyaPageEditorAddView,
      JinyaPageEditorItem,
    },
    name: 'JinyaSegmentPageEditor',
    methods: {
      scroll($event) {
        if (!$event.deltaX && !this.addModal.show && !this.editModal.show) {
          this.$refs.editor.scrollBy({
            behavior: 'auto',
            left: $event.deltaY > 0 ? 100 : -100,
          });
        }
      },
      async move(segment, oldPosition, newPosition) {
        if (oldPosition < newPosition) {
          this.segments.splice(newPosition + 1, 0, segment);
          this.segments.splice(oldPosition, 1);
        } else {
          this.segments.splice(newPosition, 0, segment);
          this.segments.splice(oldPosition + 1, 1);
        }
        await JinyaRequest.put(`/api/segment_page/${this.$route.params.slug}/segment/${segment.id}/${oldPosition}`, {
          position: newPosition,
        });
      },
      async saveAdd(type) {
        const segment = {
          type,
          readOnly: true,
        };
        if (type === 'artwork') {
          segment.action = 'none';
        }

        this.segments.splice(this.currentPosition + 1, 0, segment);
        this.addModal.show = false;
      },
      async deleteArtwork(position, id) {
        await JinyaRequest.delete(`/api/segment_age/${this.$route.params.slug}/segment/${id}`);

        this.artworks.splice(position, 1);
      },
      async saveEdit(segment, index) {
        // eslint-disable-next-line max-len
        await JinyaRequest.put(`/api/segment_page/${this.$route.params.slug}/segment/${segment.id}/${index}`, segment);

        this.artworks.splice(this.currentPosition, 1, {
          segment,
          id: segment.id,
        });
        this.editModal.show = false;
      },
      add(position) {
        this.addModal.show = true;
        this.addModal.loading = true;
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
        state: '',
        message: '',
        currentPosition: -1,
        loading: false,
        addModal: {
          show: false,
          loading: false,
        },
        editModal: {
          show: false,
          loading: false,
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
</style>
