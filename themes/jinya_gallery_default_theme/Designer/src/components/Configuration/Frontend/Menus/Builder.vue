<template>
    <jinya-editor>
        <jinya-loader :loading="loading"/>
        <jinya-form v-if="!loading" save-label="configuration.frontend.menus.builder.save"
                    cancel-label="configuration.frontend.menus.builder.cancel">
            <jinya-editor-pane>
                <jinya-tab-container :items="types" @select="selectTemplateItems"
                                     :class="{'is--loading': itemsLoading}">
                    <jinya-loader v-if="itemsLoading" :loading="itemsLoading"></jinya-loader>
                    <draggable v-model="selectedTemplateItems" :options="templateItemsOptions" v-else
                               class="jinya-menu-builder__list">
                        <jinya-menu-builder-template-entry v-for="item in selectedTemplateItems" :item="item"
                                                           :key="`${item.title}-${item.route.name}`"/>
                    </draggable>
                </jinya-tab-container>
            </jinya-editor-pane>
            <jinya-editor-pane>
                <span class="jinya-menu-builder__header">{{'configuration.frontend.menus.builder.menu'|jmessage}}</span>
                <draggable v-model="items" :options="itemsOptions" class="jinya-menu-builder__list" @add="itemsAdded"
                           @change="itemsChange" @start="itemDragStart" @end="itemDragEnd">
                    <jinya-menu-builder-group v-for="item in items" :item="item"
                                              :key="`${item.position}-${item.parent.id}-${item.route.name}`"/>
                </draggable>
            </jinya-editor-pane>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaMenuBuilderGroup from "@/components/Configuration/Frontend/Menus/Builder/BuilderGroup";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import JinyaForm from "@/framework/Markup/Form/Form";
  import JinyaEditor from "@/framework/Markup/Form/Editor";
  import JinyaEditorPane from "@/framework/Markup/Form/EditorPane";
  import draggable from 'vuedraggable';
  import JinyaMenuBuilderTemplateEntry from "@/components/Configuration/Frontend/Menus/Builder/TemplateEntry";
  import JinyaTabContainer from "@/framework/Markup/Tab/TabContainer";
  import DOMUtils from "@/framework/Utils/DOMUtils";
  import EventBus from "@/framework/Events/EventBus";
  import Events from "@/framework/Events/Events";
  import JinyaLoader from "@/framework/Markup/Loader";
  import Translator from "@/framework/i18n/Translator";

  export default {
    name: "Builder",
    components: {
      JinyaLoader,
      JinyaTabContainer,
      JinyaMenuBuilderTemplateEntry,
      JinyaEditorPane,
      JinyaEditor,
      JinyaForm,
      JinyaMenuBuilderGroup,
      draggable
    },
    data() {
      return {
        menu: {
          name: '',
          id: -1,
          children: []
        },
        items: [],
        selectedTemplateItems: [],
        galleries: [],
        forms: [],
        pages: [],
        itemsLoading: false,
        loading: false,
        actions: [],
        absOldClientX: 0,
        absClientX: 0
      }
    },
    async mounted() {
      this.loading = true;
      this.menu = await JinyaRequest.get(`/api/menu/${this.$route.params.id}`);

      const flattenChildren = (parent, nestingLevel, items) => {
        const flatten = item => {
          const elem = {
            id: item.id,
            title: item.title,
            parent: {
              type: nestingLevel === 0 ? 'menu' : 'item',
              id: parent.id
            },
            highlighted: item.highlighted,
            nestingLevel: nestingLevel,
            pageType: item.pageType,
            position: item.position,
            route: item.route
          };

          return [elem].concat(flattenChildren(item, nestingLevel + 1, item.children));
        };

        return items
          .map(item => flatten(item, items))
          .reduce((acc, val) => acc.concat(val), [])
          .filter(item => !Array.isArray(item));
      };

      this.items = flattenChildren(this.menu, 0, this.menu.children);

      DOMUtils.changeTitle(this.menu.name);
      EventBus.$emit(Events.header.change, this.menu.name);
      this.loading = false;
    },
    computed: {
      types() {
        return [
          {
            title: Translator.message('configuration.frontend.menus.builder.galleries'),
            name: 'galleries'
          },
          {
            title: Translator.message('configuration.frontend.menus.builder.forms'),
            name: 'forms'
          },
          {
            title: Translator.message('configuration.frontend.menus.builder.pages'),
            name: 'pages'
          },
          {
            title: Translator.message('configuration.frontend.menus.builder.other'),
            name: 'other'
          }
        ]
      },
      templateItemsOptions() {
        return {
          group: {
            name: 'menu',
            pull: 'clone',
            put: false
          },
          ghostClass: 'is--dragging',
          sort: false
        }
      },
      itemsOptions() {
        return {
          group: 'menu',
          ghostClass: 'is--dragging'
        }
      }
    },
    methods: {
      itemDragStart(event) {
        this.currentItem = this.items[event.oldIndex];
        this.absOldClientX = 0;
        this.absClientX = 0;
        document.ondragover = event => {
          this.absClientX = Math.abs(event.clientX);

          //TODO Implement correct movement on x axis

          this.itemDragMove();
        };
      },
      itemDragMove() {
        const item = this.currentItem;

        const currentIdx = this.items.findIndex(elem => item.position === elem.position && item.parent.id === elem.parent.id);

        if (this.absOldClientX > this.absClientX) {
          const previous = (currentIdx => {
            if (this.items.length > currentIdx && currentIdx > 0) {
              return this.items[currentIdx - 1];
            }

            return false;
          })(currentIdx);

          if (previous && previous.nestingLevel - 1 === item.nestingLevel) {
            item.nestingLevel = item.nestingLevel + 1;
          }
        } else if (this.absOldClientX < this.absClientX && item.nestingLevel > 0) {
          item.nestingLevel = item.nestingLevel - 1;
        }

        this.absOldClientX = this.absClientX;
      },
      itemDragEnd() {
        delete this.currentItem;
        document.ondragover = () => {
        };
      },
      itemsAdded(add) {
        const position = add.newIndex;
        const item = this.items[position];

        const clone = ((item) => {
          const clone = JSON.parse(JSON.stringify(item));
          clone.position = position;

          if (position === 0 || position === this.items.length) {
            clone.nestingLevel = 0;
            clone.parent = {
              id: this.menu.id,
              type: 'menu'
            };
          } else {
            const previous = this.items[position + 1];
            clone.nestingLevel = previous.nestingLevel;
            clone.parent = previous.parent;
          }

          return clone;
        })(item);

        this.items.splice(position, 1, clone);
      },
      itemsChange(data) {
        const moved = data.moved;

        if (moved) {
          const newPosition = moved.newIndex;

          const item = this.items[newPosition];

          if (newPosition === 0 || newPosition === this.items.length) {
            item.nestingLevel = 0;
            item.parent = {
              id: this.menu.id,
              type: 'menu'
            };
          } else {
            const previous = this.items[newPosition + 1];
            item.nestingLevel = previous.nestingLevel;
            item.parent = previous.parent;
          }
        }
      },
      generateTemplateItem(type, title, slug) {
        return {
          title: title,
          pageType: type,
          route: {
            name: `frontend_${type}_details`,
            parameter: {
              slug: slug
            },
            url: `/${type}/${slug}`
          },
          parent: {}
        }
      },
      async selectTemplateItems(type) {
        this.itemsLoading = true;

        if (type === 'galleries' && this.galleries.length === 0) {
          const galleries = await
            JinyaRequest.get(`/api/gallery?count=${Number.MAX_SAFE_INTEGER}`);
          this.galleries = galleries.items.map(item => this.generateTemplateItem('gallery', item.name, item.slug));

          this.selectedTemplateItems = this.galleries;
        } else if (type === 'forms' && this.forms.length === 0) {
          const forms = await
            JinyaRequest.get(`/api/form?count=${Number.MAX_SAFE_INTEGER}`);
          this.forms = forms.items.map(item => this.generateTemplateItem('form', item.title, item.slug));

          this.selectedTemplateItems = this.forms;
        } else if (type === 'pages' && this.pages.length === 0) {
          const pages = await
            JinyaRequest.get(`/api/page?count=${Number.MAX_SAFE_INTEGER}`);
          this.pages = pages.items.map(item => this.generateTemplateItem('page', item.title, item.slug));

          this.selectedTemplateItems = this.pages;
        } else if (type === 'other') {
          const baseItem = {
            route: {
              name: '#',
              parameter: {},
              url: ''
            }
          };

          this.selectedTemplateItems = [
            Object.assign({}, baseItem, {
              title: Translator.message('configuration.frontend.menus.builder.external'),
              pageType: 'external'
            }),
            Object.assign({}, baseItem, {
              title: Translator.message('configuration.frontend.menus.builder.group'),
              pageType: 'empty'
            })
          ];
        }

        this.itemsLoading = false;
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-tab {
        &.is--loading {
            height: 15rem;
        }
    }

    .is--dragging {
        opacity: 0.4;
    }

    .jinya-menu-builder__list {
        width: 100%;
    }

    .jinya-menu-builder__header {
        font-size: 1.4rem;
        padding: 0.5em 1em 0;
        margin-bottom: 1.2rem;
        width: 100%;
        color: $primary;
        border: 2px solid transparent;
        border-bottom-color: $primary;
    }
</style>