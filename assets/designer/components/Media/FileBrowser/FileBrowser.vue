<template>
    <div class="jinya-file-browser">
        <jinya-loader :loading="loading" v-if="loading"/>
        <template v-else>
            <file-view :files="files" @deleteFile="showDeleteFileDialog" @editFile="showEditFileDialog"
                       @fileChanged="selectFile"/>
            <preview-pane :file="selectedFile" v-if="selectedFile"/>
            <jinya-floating-action-button :is-primary="true" @click="showAddFileDialog" icon="file-upload-outline"/>
            <jinya-modal :loading="addFileDialog.loading" @close="addFileDialog.visible = false"
                         title="media.files.add.title" v-if="addFileDialog.visible">
                <jinya-message :message="addFileDialog.message" :state="addFileDialog.state" slot="message"
                               v-if="addFileDialog.message"/>
                <jinya-form>
                    <jinya-input :enable="!addFileDialog.loading" label="media.files.add.name" type="text"
                                 v-model="addFileDialog.name"/>
                    <jinya-file-input :enable="!addFileDialog.loading"
                                      @picked="data => addFileDialog.file = data.item(0)" label="media.files.add.file"/>
                </jinya-form>
                <jinya-modal-button :closes-modal="true" :is-disabled="addFileDialog.loading" :is-secondary="true"
                                    label="media.files.add.cancel" slot="buttons-left"/>
                <jinya-modal-button :is-disabled="addFileDialog.loading" :is-primary="true" @click="addFile"
                                    label="media.files.add.upload" slot="buttons-right"/>
            </jinya-modal>
            <jinya-modal :loading="editFileDialog.loading" @close="editFileDialog.visible = false"
                         title="media.files.edit.title" v-if="editFileDialog.visible">
                <jinya-message :message="editFileDialog.message" :state="editFileDialog.state" slot="message"
                               v-if="editFileDialog.message"/>
                <jinya-form>
                    <jinya-input :enable="!editFileDialog.loading" label="media.files.edit.name" type="text"
                                 v-model="editFileDialog.name"/>
                    <jinya-file-input :enable="!editFileDialog.loading" :has-value="true"
                                      @picked="data => editFileDialog.file = data.item(0)"
                                      label="media.files.edit.file"/>
                </jinya-form>
                <jinya-modal-button :closes-modal="true" :is-disabled="editFileDialog.loading" :is-secondary="true"
                                    label="media.files.edit.cancel" slot="buttons-left"/>
                <jinya-modal-button :is-disabled="editFileDialog.loading" :is-primary="true" @click="editFile"
                                    label="media.files.edit.upload" slot="buttons-right"/>
            </jinya-modal>
            <jinya-modal :loading="deleteFileDialog.loading" @close="deleteFileDialog.visible = false"
                         title="media.files.delete.title" v-if="deleteFileDialog.visible">
                <jinya-message :message="deleteFileDialog.error" slot="message" state="error"
                               v-if="deleteFileDialog.error && !deleteFileDialog.loading"/>
                {{'media.files.delete.content'|jmessage(deleteFileDialog.file)}}
                <jinya-modal-button :closes-modal="true" :is-disabled="deleteFileDialog.loading" :is-secondary="true"
                                    label="media.files.delete.no" slot="buttons-left"/>
                <jinya-modal-button :is-danger="true" :is-disabled="deleteFileDialog.loading" @click="deleteFile"
                                    label="media.files.delete.yes" slot="buttons-right"/>
            </jinya-modal>
        </template>
    </div>
</template>

<script>
  import FileView from '@/components/Media/FileBrowser/FileView';
  import PreviewPane from '@/components/Media/FileBrowser/PreviewPane';
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaFileInput from '@/framework/Markup/Form/FileInput';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import Translator from '@/framework/i18n/Translator';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import Routes from '@/router/Routes';

  export default {
    name: 'FileBrowser',
    components: {
      JinyaLoader,
      JinyaFileInput,
      JinyaInput,
      JinyaForm,
      JinyaModalButton,
      JinyaMessage,
      JinyaModal,
      JinyaFloatingActionButton,
      PreviewPane,
      FileView,
    },
    methods: {
      showAddFileDialog() {
        this.addFileDialog.visible = true;
      },
      showEditFileDialog(file) {
        this.editFileDialog.visible = true;
        this.editFileDialog.name = file.name;
        this.editFileDialog.id = file.id;
        this.editFileDialog.originalFile = file;
      },
      showDeleteFileDialog(file) {
        this.deleteFileDialog.visible = true;
        this.deleteFileDialog.file = file;
      },
      selectFile(file) {
        this.selectedFile = file;
      },
      async addFile() {
        try {
          this.addFileDialog.state = 'loading';
          this.addFileDialog.loading = true;
          this.addFileDialog.message = Translator.message('media.files.add.creating');
          const file = {
            name: this.addFileDialog.name,
          };
          const savedFile = await JinyaRequest.post('/api/media/file', file);
          this.addFileDialog.id = savedFile.id;
          this.addFileDialog.message = Translator.message('media.files.add.uploading', savedFile);
          await this.upload(this.addFileDialog);
          this.files.push(savedFile);
          this.addFileDialog.loading = false;
          this.addFileDialog.visible = false;
          this.addFileDialog = {
            visible: false,
            name: '',
            message: '',
            state: '',
            file: null,
            id: -1,
          };
        } catch (e) {
          this.addFileDialog.loading = false;
          this.addFileDialog.message = e.message;
          this.addFileDialog.state = 'error';
        }
      },
      async editFile() {
        try {
          this.editFileDialog.state = 'loading';
          this.editFileDialog.loading = true;
          if (this.editFileDialog.originalFile.name !== this.editFileDialog.name) {
            this.editFileDialog.message = Translator.message('media.files.edit.renaming', {
              oldName: this.editFileDialog.originalFile.name,
              newName: this.editFileDialog.name,
            });
            const file = {
              name: this.editFileDialog.name,
            };
            await JinyaRequest.put(`/api/media/file/${this.editFileDialog.id}`, file);
          }
          const savedFile = await JinyaRequest.get(`/api/media/file/${this.editFileDialog.id}`);
          this.editFileDialog.id = savedFile.id;
          if (this.editFileDialog.file) {
            this.editFileDialog.message = Translator.message('media.files.edit.uploading', this.editFileDialog);
            await this.upload(this.editFileDialog);
          }
          const idx = this.files.findIndex((item) => item.id === this.editFileDialog.id);
          this.files.splice(idx, 1, savedFile);
          this.editFileDialog.loading = false;
          this.editFileDialog.visible = false;
          this.editFileDialog = {
            visible: false,
            name: '',
            message: '',
            state: '',
            file: null,
            id: -1,
          };
        } catch (e) {
          this.editFileDialog.loading = false;
          this.editFileDialog.message = e.message;
          this.editFileDialog.state = 'error';
        }
      },
      async upload({ id, file }) {
        await JinyaRequest.post(`/api/media/file/${id}/content`);
        await JinyaRequest.upload(`/api/media/file/${id}/content/0`, file);
        await JinyaRequest.put(`/api/media/file/${id}/content/finish`);
        // TODO: Need to add worker upload for bigger files
      },
      async deleteFile() {
        try {
          this.deleteFileDialog.loading = true;
          await JinyaRequest.delete(`/api/media/file/${this.deleteFileDialog.file.id}`);
          const file = this.files.findIndex((item) => item.id === this.deleteFileDialog.file.id);
          this.files.splice(file, 1);
          this.deleteFileDialog.loading = false;
          this.deleteFileDialog.visible = false;
        } catch (e) {
          this.deleteFileDialog.loading = false;
          this.deleteFileDialog.error = e.message;
        }
      },
      async fetchFiles(keyword) {
        this.loading = true;
        const data = await JinyaRequest.get(`/api/media/file?keyword=${keyword}`);
        this.files = data.items;
        this.loading = false;
      },
    },
    async mounted() {
      await this.fetchFiles(this.$route.query.keyword || '');
      EventBus.$on(Events.search.triggered, (value) => {
        this.$router.push({
          name: Routes.Media.Files.FileBrowser.name,
          query: {
            keyword: value.keyword,
          },
        });
      });
    },
    async beforeRouteUpdate(to, from, next) {
      await this.fetchFiles(to.query.keyword || '');
      next();
    },
    data() {
      return {
        selectedFile: {},
        loading: true,
        files: [],
        addFileDialog: {
          visible: false,
          name: '',
          error: '',
          file: null,
          id: -1,
          loading: false,
          state: null,
        },
        editFileDialog: {
          visible: false,
          name: '',
          error: '',
          file: null,
          id: -1,
          loading: false,
          originalFile: null,
          state: null,
        },
        deleteFileDialog: {
          visible: false,
          file: {},
          loading: false,
          error: '',
        },
      };
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-file-browser {
        display: flex;
    }
</style>
