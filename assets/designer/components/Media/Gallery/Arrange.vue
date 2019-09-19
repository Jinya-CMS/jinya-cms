<template>
    <div class="jinya-gallery__designer">
        <h1 class="jinya-gallery-designer__name">{{gallery.name}}</h1>
        <aside class="jinya-gallery-designer__files">
            <draggable :sort="false" @change="changeFile" class="jinya-gallery-designer__files-list" group="gallery"
                       v-model="files">
                <div :key="`file-${file.file.id}`" class="jinya-gallery-designer__file" v-for="file in files">
                    <span class="jinya-gallery-designer__file-header">{{file.file.name}}</span>
                    <img :alt="file.file.name" :src="file.file.path"
                         class="jinya-gallery-designer__file-image" v-if="file.file.type.startsWith('image')">
                </div>
            </draggable>
        </aside>
        <section class="jinya-gallery-designer__positions">
            <draggable @change="changePosition" class="jinya-gallery-designer__positions-list" group="gallery"
                       v-model="gallery.files">
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
      changeFile(event) {
        const { added } = event;
        if (added) {
          this.files.splice(added.newIndex, 1);
        }
      },
      async changePosition(event) {
        const { added, removed, moved } = event;
        if (added) {
          const { element, newIndex } = added;
          const position = await JinyaRequest.post(`/api/media/gallery/file/${this.$route.params.id}/file`, {
            position: newIndex - 1,
            file: element.file.id,
          });

          const idx = this.gallery.files.findIndex((item) => item.name === element.name);
          this.gallery.files.splice(idx, 1, position);
        } else if (removed) {
          const { element } = removed;
          await JinyaRequest.delete(`/api/media/gallery/file/${this.$route.params.id}/file/${element.id}`);
          this.gallery.files.remove(element);
        } else if (moved) {
          const { newIndex, oldIndex, element } = moved;
          await JinyaRequest.put(`/api/media/gallery/file/${this.$route.params.id}/file/${element.id}/${oldIndex}`, {
            position: newIndex,
          });
        }
      },
    },
    async mounted() {
      const [files, gallery] = await Promise.all([
        JinyaRequest.get('/api/media/file'),
        JinyaRequest.get(`/api/media/gallery/${this.$route.params.id}`),
      ]);
      this.files = files.items.map((file) => ({
        id: -1,
        file,
      }));
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
        grid-template-rows: 3rem auto;
        padding-top: 3rem;
        height: 100%;
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
        margin-bottom: 1rem;
        border-radius: 10px;
        overflow: hidden;
        cursor: move;
        height: 18rem;

        &:nth-child(1n) {
            margin-right: 1rem;
        }

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
        flex-basis: 15rem;
        width: 15rem;
    }

    .jinya-gallery-designer__files {
        grid-column: 1/2;
        height: 100%;
        overflow: auto;
        padding-bottom: 2rem;
        padding-right: 1rem;
        padding-left: 0.5rem;
        box-sizing: border-box;
    }

    .jinya-gallery-designer__files-list,
    .jinya-gallery-designer__positions-list {
        display: flex;
        flex-flow: row wrap;
        width: 100%;
        min-height: 2rem;
        padding-top: 0.5rem;

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
        height: 100%;
        border-left: 1px solid $primary-lighter;
        padding-left: 1.5rem;
        box-sizing: border-box;
        overflow: auto;
    }
</style>
