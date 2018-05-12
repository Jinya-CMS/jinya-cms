<template>
    <jinya-editor class="jinya-menu-builder">
        <jinya-loader :loading="loading"/>
        <jinya-form v-if="!loading" save-label="configuration.frontend.menus.builder.save" @submit="save" @back="back"
                    cancel-label="configuration.frontend.menus.builder.cancel" button-bar-padding-right="0.5rem">
            <draggable v-show="drag" class="jinya-menu-builder__trash" :options="itemsOptions">
                <i class="mdi mdi-delete is--big"></i>
                <span>{{'configuration.frontend.menus.builder.delete'|jmessage}}</span>
            </draggable>
            <jinya-message :message="message" :state="state"/>
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
                           @change="itemsChange" @start="drag = true" @end="drag = false">
                    <jinya-menu-builder-group v-for="(item, index) in items" :item="item" :enable="enable"
                                              @increase="increase(item)" :allow-increase="item.allowIncrease"
                                              @decrease="decrease(item)" :allow-decrease="item.allowDecrease"
                                              :key="`${item.position}-${index}-${item.route.name}`"/>
                </draggable>
            </jinya-editor-pane>
        </jinya-form>
        <jinya-modal title="configuration.frontend.menus.builder.leave.title" v-if="leaving">
            {{'configuration.frontend.menus.builder.leave.content'|jmessage(menu)}}
            <template slot="buttons-left">
                <jinya-modal-button label="configuration.frontend.menus.builder.leave.cancel" :closes-modal="true"
                                    @click="stay" :is-secondary="true"/>
            </template>
            <template slot="buttons-right">
                <jinya-modal-button label="configuration.frontend.menus.builder.leave.no" :closes-modal="true"
                                    @click="stayAndSaveChanges" :is-success="true"/>
                <jinya-modal-button label="configuration.frontend.menus.builder.leave.yes" :closes-modal="true"
                                    @click="leave" :is-danger="true"/>
            </template>
        </jinya-modal>
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
  import ObjectUtils from "@/framework/Utils/ObjectUtils";
  import JinyaMessage from "@/framework/Markup/Validation/Message";
  import Routes from "@/router/Routes";
  import JinyaModal from "@/framework/Markup/Modal/Modal";
  import JinyaModalButton from "@/framework/Markup/Modal/ModalButton";
  import Timing from "@/framework/Utils/Timing";

  export default {
    name: "Builder",
    components: {
      JinyaModalButton,
      JinyaModal,
      JinyaMessage,
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
        drag: false,
        state: '',
        message: '',
        enable: true,
        leaving: false
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
            highlighted: item.highlighted,
            nestingLevel: nestingLevel,
            pageType: item.pageType,
            position: item.position,
            route: item.route,
            allowIncrease: nestingLevel < 0,
            allowDecrease: nestingLevel > 0
          };

          return [elem].concat(flattenChildren(item, nestingLevel + 1, item.children));
        };

        return items
          .map(item => flatten(item, items))
          .reduce((acc, val) => acc.concat(val), [])
          .filter(item => !Array.isArray(item));
      };

      this.items = flattenChildren(this.menu, 0, this.menu.children);
      this.originalItems = ObjectUtils.clone(this.items);
      this.calculateNestingAllowance();

      DOMUtils.changeTitle(this.menu.name);
      EventBus.$emit(Events.header.change, this.menu.name);
      this.loading = false;
    },
    beforeRouteLeave(to, from, next) {
      if (!ObjectUtils.equals(this.items, this.originalItems)) {
        this.leaving = true;
        this.next = next;
      } else {
        next();
      }
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
      calculateNestingAllowance() {
        for (let i = 0; i < this.items.length; i++) {
          const previous = this.findPrevious(this.items[i], i);
          const item = this.items[i];

          item.allowIncrease = previous && previous.nestingLevel >= item.nestingLevel;
          item.allowDecrease = !this.hasChildren(item, i) && !!this.findParent(item, i);
        }
      },
      hasChildren(item, currentIdx = undefined) {
        if (currentIdx === undefined)
          currentIdx = this.items.findIndex(elem => ObjectUtils.equals(elem, item));

        return item.nestingLevel < this.findNext(item, currentIdx)?.nestingLevel;
      },
      findParent(item, currentIdx = undefined) {
        if (currentIdx === undefined)
          currentIdx = this.items.findIndex(elem => ObjectUtils.equals(elem, item));

        return this.items
          .slice(0, currentIdx)
          .reverse()
          .find(elem => elem.nestingLevel === item.nestingLevel - 1);
      },
      findPrevious(item, currentIdx = undefined) {
        if (currentIdx === undefined)
          currentIdx = this.items.findIndex(elem => ObjectUtils.equals(elem, item));

        if (this.items.length > currentIdx && currentIdx > 0) {
          return this.items[currentIdx - 1];
        }

        return false;
      },
      findNext(item, currentIdx = undefined) {
        if (currentIdx === undefined)
          currentIdx = this.items.findIndex(elem => ObjectUtils.equals(elem, item));

        if (this.items.length > currentIdx && currentIdx < this.items.length) {
          return this.items[currentIdx + 1];
        }

        return false;
      },
      increase(item) {
        item.nestingLevel = item.nestingLevel + 1;
        this.calculateNestingAllowance();
      },
      decrease(item) {
        item.nestingLevel = item.nestingLevel - 1;
        this.calculateNestingAllowance();
      },
      itemsAdded(add) {
        const position = add.newIndex;
        const item = this.items[position];

        const clone = ((item) => {
          const clone = JSON.parse(JSON.stringify(item));
          clone.position = position;
          clone.showSettings = true;

          if (position === 0 || position === this.items.length) {
            clone.nestingLevel = 0;
          } else {
            const previous = this.items[position + 1];
            clone.nestingLevel = previous.nestingLevel;
          }

          return clone;
        })(item);

        this.items.splice(position, 1, clone);

        this.calculateNestingAllowance();
      },
      itemsChange(data) {
        const moved = data.moved;

        if (moved) {
          const newPosition = moved.newIndex;
          const item = this.items[newPosition];

          if (newPosition === 0 || newPosition === this.items.length) {
            item.nestingLevel = 0;
          } else {
            const previous = this.items[newPosition + 1];
            item.nestingLevel = previous.nestingLevel;
          }
        }

        this.calculateNestingAllowance();
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
      },
      async save() {
        this.state = 'loading';
        this.enable = false;
        try {
          this.message = Translator.message('configuration.frontend.menus.builder.saving', this.menu);
          await JinyaRequest.put(`/api/menu/${this.menu.id}/items/batch`, this.items);
          this.state = 'success';
          this.message = Translator.message('configuration.frontend.menus.builder.saved', this.menu);
          await Timing.wait();
          this.originalItems = this.items;
          this.$router.push(Routes.Configuration.Frontend.Menu.Overview);
        } catch (error) {
          this.message = Translator.message(`configuration.frontend.menus.builder.${error.message}`, this.menu);
          this.state = 'error';
          this.enable = true;
        }
      },
      back() {
        this.$router.push(Routes.Configuration.Frontend.Menu.Overview);
      },
      stayAndSaveChanges() {
        this.stay();
        this.save();
      },
      stay() {
        this.next(false);
        this.leaving = false;
      },
      leave() {
        this.next();
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

    .jinya-menu-builder__trash {
        width: 100%;
        display: flex;
        transition: opacity 0.3s;
        color: $danger;
        justify-content: center;
        flex-direction: row;
        align-items: center;
    }

    .jinya-menu-builder {
        padding-bottom: 1rem;
    }
</style>