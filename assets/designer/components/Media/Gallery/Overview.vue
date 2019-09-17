<template>
    <div class="jinya-galleries">
        <jinya-toolbar>
            <jinya-toolbar-button is-primary label="media.galleries.list.toolbar.add"/>
            <jinya-toolbar-button :is-disabled="!this.selectedRow" @click="showEditModal" is-secondary
                                  label="media.galleries.list.toolbar.edit"/>
            <jinya-toolbar-button :is-disabled="!this.selectedRow" is-secondary
                                  label="media.galleries.list.toolbar.arrange"/>
            <jinya-toolbar-button :is-disabled="!this.selectedRow" @click="showDeleteModal" is-danger
                                  label="media.galleries.list.toolbar.delete"/>
        </jinya-toolbar>
        <Table :headers="tableHeaders" :rows="tableRows" :selectedRow="selectedRow" @selected="selectRow"/>
        <jinya-modal :loading="deleteGalleryDialog.loading" @close="deleteGalleryDialog.visible = false"
                     title="media.galleries.delete.title" v-if="deleteGalleryDialog.visible">
            <jinya-message :message="deleteGalleryDialog.error" slot="message" state="error"
                           v-if="deleteGalleryDialog.error && !deleteGalleryDialog.loading"/>
            {{'media.galleries.delete.content'|jmessage(selectedRow)}}
            <jinya-modal-button :closes-modal="true" :is-disabled="deleteGalleryDialog.loading" :is-secondary="true"
                                label="media.galleries.delete.no" slot="buttons-left"/>
            <jinya-modal-button :is-danger="true" :is-disabled="deleteGalleryDialog.loading" @click="deleteGallery"
                                label="media.galleries.delete.yes" slot="buttons-right"/>
        </jinya-modal>
        <jinya-modal :loading="editGalleryDialog.loading" @close="resetSelectedRow"
                     title="media.files.edit.title" v-if="editGalleryDialog.visible">
            <jinya-message :message="editGalleryDialog.message" :state="editGalleryDialog.state" slot="message"
                           v-if="editGalleryDialog.message"/>
            <jinya-form>
                <jinya-input :enable="!editGalleryDialog.loading" label="media.galleries.edit.name" type="text"
                             v-model="selectedRow.name"/>
                <jinya-choice :choices="galleryTypes" :enable="!editGalleryDialog.loading"
                              :selected="selectedRow.type" @selected="(value) => selectedRow.type = value.value"
                              label="media.galleries.edit.type"/>
                <jinya-choice :choices="galleryOrientations" :enable="!editGalleryDialog.loading"
                              :selected="selectedRow.orientation"
                              @selected="(value) => selectedRow.orientation = value.value"
                              label="media.galleries.edit.orientation"/>
                <jinya-textarea :enable="!editGalleryDialog.loading" label="media.galleries.edit.type"
                                v-model="selectedRow.description"/>
            </jinya-form>
            <jinya-modal-button :closes-modal="true" :is-disabled="editGalleryDialog.loading" :is-secondary="true"
                                label="media.files.edit.cancel" slot="buttons-left"/>
            <jinya-modal-button :is-disabled="editGalleryDialog.loading" :is-primary="true" @click="editFile"
                                label="media.files.edit.upload" slot="buttons-right"/>
        </jinya-modal>
    </div>
</template>

<script>
  import Table from '@/framework/Markup/Table/Table';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import JinyaToolbar from '@/framework/Markup/Toolbar/Toolbar';
  import JinyaToolbarButton from '@/framework/Markup/Toolbar/ToolbarButton';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import JinyaTextarea from '@/framework/Markup/Form/Textarea';

  export default {
    name: 'Galleries',
    components: {
      JinyaTextarea,
      JinyaChoice,
      JinyaInput,
      JinyaForm,
      JinyaModalButton,
      JinyaMessage,
      JinyaModal,
      JinyaToolbarButton,
      JinyaToolbar,
      Table,
    },
    computed: {
      galleryTypes() {
        return [
          {
            value: 'masonry',
            text: Translator.message('media.galleries.edit.types.masonry'),
          },
          {
            value: 'sequence',
            text: Translator.message('media.galleries.edit.types.sequence'),
          },
        ];
      },
      galleryOrientations() {
        return [
          {
            value: 'vertical',
            text: Translator.message('media.galleries.edit.orientations.vertical'),
          },
          {
            value: 'horizontal',
            text: Translator.message('media.galleries.edit.orientations.horizontal'),
          },
        ];
      },
    },
    methods: {
      async resetSelectedRow() {
        this.selectedRow = await JinyaRequest.get(`/api/media/gallery/${this.selectedRow.id}`);
        this.editGalleryDialog.visible = false;
      },
      selectRow(row) {
        this.selectedRow = row;
      },
      showDeleteModal() {
        this.deleteGalleryDialog.visible = true;
      },
      showEditModal() {
        this.editGalleryDialog.visible = true;
      },
      async deleteGallery() {
        try {
          this.deleteGalleryDialog.loading = true;
          await JinyaRequest.delete(`/api/media/gallery/${this.selectedRow.id}`);
          const gallery = this.tableRows.findIndex((item) => item.id === this.selectedRow.id);
          this.tableRows.splice(gallery, 1);
          this.deleteGalleryDialog.loading = false;
          this.deleteGalleryDialog.visible = false;
        } catch (e) {
          this.deleteGalleryDialog.loading = false;
          this.deleteGalleryDialog.error = e.message;
        }
        await JinyaRequest.delete(`/api/media/gallery/${this.selectedRow.id}`);
      },
    },
    async mounted() {
      const data = await JinyaRequest.get('/api/media/gallery');
      this.tableRows = data.items;
    },
    data() {
      return {
        deleteGalleryDialog: {
          visible: false,
          loading: false,
          error: '',
        },
        editGalleryDialog: {
          visible: false,
          loading: false,
          message: '',
          state: '',
        },
        selectedRow: null,
        tableHeaders: [
          {
            name: 'name',
            title: Translator.message('media.galleries.list.table.header.name'),
          },
          {
            name: 'orientation',
            title: Translator.message('media.galleries.list.table.header.orientation'),
            template(row) {
              return Translator.message(`media.galleries.list.table.cells.${row.orientation}`);
            },
          },
          {
            name: 'type',
            title: Translator.message('media.galleries.list.table.header.type'),
            template(row) {
              return Translator.message(`media.galleries.list.table.cells.${row.type}`);
            },
          },
          {
            name: 'description',
            title: Translator.message('media.galleries.list.table.header.description'),
          },
        ],
        tableRows: [],
      };
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-galleries {
        padding-top: 3rem;
    }
</style>
