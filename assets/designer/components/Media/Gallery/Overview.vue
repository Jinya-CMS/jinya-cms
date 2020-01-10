<template>
    <div class="jinya-galleries">
        <jinya-toolbar>
            <jinya-toolbar-button @click="showAddModal" is-primary label="media.galleries.list.toolbar.add"/>
            <jinya-toolbar-button :is-disabled="!selectedRow" @click="showEditModal" is-secondary
                                  label="media.galleries.list.toolbar.edit"/>
            <jinya-toolbar-button :is-disabled="!selectedRow" :params="arrangeGalleryRouteParams"
                                  is-secondary label="media.galleries.list.toolbar.arrange"
                                  to="Media.Galleries.Arrange"/>
            <jinya-toolbar-button :is-disabled="!selectedRow" @click="showDeleteModal" is-danger
                                  label="media.galleries.list.toolbar.delete"/>
        </jinya-toolbar>
        <jinya-table :headers="tableHeaders" :rows="tableRows" :selectedRow="selectedRow" @selected="selectRow"/>
        <jinya-modal :loading="deleteGalleryDialog.loading" @close="deleteGalleryDialog.visible = false"
                     title="media.galleries.delete.title" v-if="deleteGalleryDialog.visible">
            <jinya-message :message="deleteGalleryDialog.error" slot="message" state="error"
                           v-if="deleteGalleryDialog.error && !deleteGalleryDialog.loading"/>
            {{'media.galleries.delete.content'|jmessage(selectedRow)}}
            <jinya-modal-button :is-disabled="deleteGalleryDialog.loading" closes-modal is-success
                                label="media.galleries.delete.no" slot="buttons-left"/>
            <jinya-modal-button :is-disabled="deleteGalleryDialog.loading" @click="deleteGallery" is-danger
                                label="media.galleries.delete.yes" slot="buttons-right"/>
        </jinya-modal>
        <jinya-modal :loading="editGalleryDialog.loading" @close="resetSelectedRow"
                     title="media.galleries.edit.title" v-if="editGalleryDialog.visible">
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
            <jinya-modal-button :is-disabled="editGalleryDialog.loading" closes-modal is-secondary
                                label="media.galleries.edit.cancel" slot="buttons-left"/>
            <jinya-modal-button :is-disabled="editGalleryDialog.loading" @click="editGallery" is-primary
                                label="media.galleries.edit.update" slot="buttons-right"/>
        </jinya-modal>
        <jinya-modal :loading="addGalleryDialog.loading" title="media.galleries.add.title"
                     v-if="addGalleryDialog.visible">
            <jinya-message :message="addGalleryDialog.message" :state="addGalleryDialog.state" slot="message"
                           v-if="addGalleryDialog.message"/>
            <jinya-form>
                <jinya-input :enable="!addGalleryDialog.loading" label="media.galleries.add.name" type="text"
                             v-model="addGalleryDialog.gallery.name"/>
                <jinya-choice :choices="galleryTypes" :enable="!addGalleryDialog.loading"
                              :selected="addGalleryDialog.gallery.type"
                              @selected="(value) => addGalleryDialog.gallery.type = value.value"
                              label="media.galleries.add.type"/>
                <jinya-choice :choices="galleryOrientations" :enable="!addGalleryDialog.loading"
                              :selected="addGalleryDialog.gallery.orientation"
                              @selected="(value) => addGalleryDialog.gallery.orientation = value.value"
                              label="media.galleries.add.orientation"/>
                <jinya-textarea :enable="!addGalleryDialog.loading" label="media.galleries.add.description"
                                v-model="addGalleryDialog.gallery.description"/>
            </jinya-form>
            <jinya-modal-button :is-disabled="addGalleryDialog.loading" @click="closeAddDialog" is-secondary
                                label="media.galleries.add.cancel" slot="buttons-left"/>
            <jinya-modal-button :is-disabled="addGalleryDialog.loading" @click="addGallery" is-primary
                                label="media.galleries.add.save" slot="buttons-right"/>
        </jinya-modal>
    </div>
</template>

<script>
    import JinyaTable from '@/framework/Markup/Table/Table';
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
    import Routes from '@/router/Routes';
    import EventBus from '@/framework/Events/EventBus';
    import Events from '@/framework/Events/Events';

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
      JinyaTable,
    },
    computed: {
      arrangeGalleryRoute() {
        return Routes.Media.Galleries.Arrange.name;
      },
      arrangeGalleryRouteParams() {
        return { id: this.selectedRow?.id };
      },
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
      closeAddDialog() {
        this.addGalleryDialog = {
          gallery: {
            name: '',
            orientation: '',
            type: '',
            description: '',
          },
          visible: false,
        };
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
      showAddModal() {
        this.addGalleryDialog.visible = true;
      },
      async deleteGallery() {
        try {
          this.deleteGalleryDialog.loading = true;
          await JinyaRequest.delete(`/api/media/gallery/${this.selectedRow.slug}`);
          const gallery = this.tableRows.findIndex((item) => item.id === this.selectedRow.id);
          this.tableRows.splice(gallery, 1);
          this.deleteGalleryDialog.loading = false;
          this.deleteGalleryDialog.visible = false;
          this.selectedRow = null;
        } catch (e) {
          this.deleteGalleryDialog.loading = false;
          this.deleteGalleryDialog.error = e.message;
        }
      },
      async editGallery() {
        try {
          this.editGalleryDialog.message = '';
          this.editGalleryDialog.loading = true;
          await JinyaRequest.put(`/api/media/gallery/${this.selectedRow.slug}`, this.selectedRow);
          this.editGalleryDialog.loading = false;
          this.editGalleryDialog.visible = false;
        } catch (e) {
          this.editGalleryDialog.loading = false;
          this.editGalleryDialog.message = e.message;
          this.editGalleryDialog.state = 'error';
        }
      },
      async addGallery() {
        try {
          this.addGalleryDialog.message = '';
          this.addGalleryDialog.loading = true;
          const gallery = await JinyaRequest.post('/api/media/gallery', this.addGalleryDialog.gallery);
          this.tableRows.push(gallery);
          this.addGalleryDialog.loading = false;
          this.addGalleryDialog.visible = false;
          this.closeAddDialog();
        } catch (e) {
          this.addGalleryDialog.loading = false;
          this.addGalleryDialog.message = e.message;
          this.addGalleryDialog.state = 'error';
        }
      },
      async load(keyword = '') {
        const data = await JinyaRequest.get(`/api/media/gallery?keyword=${encodeURIComponent(keyword)}`);
        this.tableRows = data.items;
      },
    },
    async mounted() {
      await this.load();
      EventBus.$on(Events.search.triggered, (value) => {
        this.$router.push({
          name: Routes.Media.Galleries.Overview.name,
          query: {
            keyword: value.keyword,
          },
        });
      });
    },
    async beforeRouteUpdate(to, from, next) {
      await this.load(to.query.keyword || '');
      next();
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
        addGalleryDialog: {
          visible: false,
          loading: false,
          message: '',
          state: '',
          gallery: {
            name: '',
            orientation: '',
            type: '',
            description: '',
          },
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
