<template>
    <div>
        <span class="jinya-page-editor__preview-header">{{gallery.name}}</span>
        <jinya-loader loading="true" v-if="loading"/>
        <div class="jinya-page-editor__preview-gallery" v-else>
            <template v-for="position in files">
                <img :alt="position.file.name" :key="position.file.id" :src="position.file.path"
                     class="jinya-page-editor__preview-image" v-if="position.file.type.startsWith('image')"/>
                <span :key="position.file.id" v-else>{{position.file.name}}</span>
            </template>
        </div>
    </div>
</template>

<script>
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';

  export default {
    name: 'JinyaPageEditorPreviewElementGallery',
    components: { JinyaLoader },
    props: {
      gallery: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        files: [],
        loading: false,
      };
    },
    async mounted() {
      this.loading = true;

      this.files = await JinyaRequest.get(`/api/media/gallery/file/${this.gallery.id}/file`);

      this.loading = false;
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page-editor__preview-header {
        font-size: 2rem;
        font-weight: lighter;
        margin-bottom: 1rem;
    }

    .jinya-page-editor__preview-gallery {
        display: flex;
        flex-flow: row wrap;
        margin: -0.5rem;
    }

    .jinya-page-editor__preview-image {
        object-fit: cover;
        flex: 1 1 7.5rem;
        padding: 0.5rem;
        width: 5rem;
    }
</style>
