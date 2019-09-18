<template>
    <div class="jinya-gallery__designer">
        <h1 class="jinya-gallery-designer__name">{{gallery.name}}</h1>
        <aside class="jinya-gallery-designer__files">
            <draggable class="jinya-gallery-designer__files-list" group="gallery" v-model="files">
                <div :key="`file-${file.id}`" class="jinya-gallery-designer__file" v-for="file in files">
                    <span class="jinya-gallery-designer__file-header">{{file.name}}</span>
                    <img :alt="file.name" :src="file.path"
                         class="jinya-gallery-designer__file-image" v-if="file.type.startsWith('image')">
                </div>
            </draggable>
        </aside>
        <section class="jinya-gallery-designer__positions">
            <draggable @add="addPosition" @remove="removePosition" @sort="movePosition"
                       class="jinya-gallery-designer__positions-list" group="gallery" v-model="gallery.files">
                <div :key="`position-${filePosition.id}`" class="jinya-gallery-designer__position"
                     v-for="filePosition in gallery.files">
                    <span class="jinya-gallery-designer__position-header">{{filePosition.file.name}}</span>
                    <img :alt="filePosition.file.name" :src="filePosition.file.path"
                         class="jinya-gallery-designer__position-image"
                         v-if="filePosition.file.type.startsWith('image')">
                </div>
            </draggable>
        </section>
    </div>
</template>

<script>
  import draggable from 'vuedraggable';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';

  export default {
    name: 'Arrange',
    components: {
      draggable,
    },
    methods: {
      async removePosition(event) {
        const { removed: { element } } = event;
        await JinyaRequest.delete(`/api/media/gallery/${this.$route.params.id}/file/${element.id}`);
      },
      async movePosition(event) {
        const { moved: { oldIndex, newIndex, element } } = event;
        await JinyaRequest.put(`/api/media/gallery/${this.$route.params.id}/file/${element.id}/${oldIndex}`, {
          position: newIndex,
        });
      },
      async addPosition(event) {
        const { added: { newIndex, element } } = event;
        await JinyaRequest.post(`/api/media/gallery/${this.$route.params.id}/file`, {
          position: newIndex,
          file: element.id,
        });
      },
    },
    async mounted() {
      const [files, gallery] = await Promise.all([
        JinyaRequest.get('/api/media/file'),
        JinyaRequest.get(`/api/media/gallery/${this.$route.params.id}`),
      ]);
      this.files = files.items;
      this.gallery = gallery;
    },
    data() {
      return {
        gallery: {},
        files: [],
      };
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-gallery__designer {
        display: grid;
        grid-template-columns: 30% 70%;
        grid-template-rows: auto auto;
        grid-gap: 1rem;
        margin-top: 3rem;
    }

    .jinya-gallery-designer__name {
        font-size: 2rem;
        margin: 0 0.5rem;
        color: $primary;
    }

    .jinya-gallery-designer__file,
    .jinya-gallery-designer__position {
        box-shadow: 0 0 1px 0 $primary;
        background: $white;
        flex: 0 0 calc(50% - 1rem);
        margin-right: 1rem;
        margin-bottom: 1rem;
        border-radius: 10px;
        overflow: hidden;
        cursor: move;

        &:hover {
            border: none;
            box-shadow: 0 0 10px 0 scale_color($primary, $alpha: 80%);
        }
    }

    .jinya-gallery-designer__file-header,
    .jinya-gallery-designer__position-header {
        font-size: 1.25rem;
        padding: 0.5rem;
        text-align: center;
        display: block;
        color: $primary;
    }

    .jinya-gallery-designer__file-image,
    .jinya-gallery-designer__position-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .jinya-gallery-designer__position {
        flex-basis: 8rem;
        height: 15rem;
        width: 15rem;
    }

    .jinya-gallery-designer__files {
        grid-column: 1/2;
    }

    .jinya-gallery-designer__files-list,
    .jinya-gallery-designer__positions-list {
        display: flex;
        flex-flow: row wrap;
        width: 100%;
        min-height: 2rem;

        &:empty {
            background: $primary-lighter;
            position: relative;
            height: 5rem;

            &::before {
                //noinspection CssNoGenericFontName
                font-family: "Material Design Icons";
                content: "\f1db";
                font-size: 3em;
                color: $primary;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(90deg);
                position: absolute;
            }
        }
    }

    .jinya-gallery-designer__positions {
        grid-column: 2/3;
    }
</style>
