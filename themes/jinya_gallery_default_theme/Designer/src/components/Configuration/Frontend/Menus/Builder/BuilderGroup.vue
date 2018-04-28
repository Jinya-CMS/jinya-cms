<template>
    <div>
        <draggable :options="draggableOptions" class="jinya-menu-builder__draggable">
        </draggable>
        <draggable :options="draggableOptions">
            <div class="jinya-menu-builder__group">
                <span class="jinya-menu-builder__title">{{item.title}}</span>
                <jinya-menu-builder-group v-for="(child, index) in item.children"
                                          :show-bottom-draggable="index - 1 === item.children.length" :item="child">
                </jinya-menu-builder-group>
                <draggable v-if="showBottomDraggable" :options="draggableOptions" class="jinya-menu-builder__draggable">
                </draggable>
            </div>
        </draggable>
    </div>
</template>

<script>
  import draggable from 'vuedraggable';

  export default {
    name: "jinya-menu-builder-group",
    components: {
      draggable
    },
    computed: {
      draggableOptions() {
        return {
          group: 'menu',
          ghostClass: 'is--dragging'
        }
      }
    },
    props: {
      showBottomDraggable: {
        type: Boolean,
        default() {
          return true;
        }
      },
      item: {
        type: Object,
        required: true
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-menu-builder__group {
        margin-left: 2rem;
        width: 30rem;

        &.is--dragging {
            opacity: 0.4;
        }
    }

    .jinya-menu-builder__title {
        display: block;
        padding: 0.5rem 0.5rem 0.5rem 1rem;
        border: 0.5px solid $white;
        background: $secondary-lighter;
        color: $gray-800;
        border-radius: 3px;
        cursor: pointer;
    }

    .jinya-menu-builder__draggable {
        min-height: 0.4rem;
        display: block;
    }
</style>