<template>
    <jinya-loader :loading="loading" v-if="loading"/>
    <div class="jinya-form-builder" v-else>
        <jinya-editor v-if="!loading">
            <jinya-form @back="back" @submit="saveChanges"
                        button-bar-padding-right="0.5rem" cancel-label="static.forms.forms.builder.cancel"
                        class="jinya-form-builder__form" novalidate
                        save-label="static.forms.forms.builder.save">
                <draggable :aria-label="'static.forms.forms.builder.delete'|jmessage"
                           :data-message="'static.forms.forms.builder.delete'|jmessage" :options="destinationOptions"
                           @add="deleteItem" class="jinya-form-builder__trash" v-show="drag">
                </draggable>
                <jinya-message :message="message" :state="state"/>
                <jinya-editor-pane>
                    <draggable :options="originOptions" class="jinya-form-builder__draggable"
                               v-model="availableItemTypes">
                        <jinya-form-builder-item :enable="enable" :item="item"
                                                 :key="`${item.type}-${index}`" :settings-available="false"
                                                 v-for="(item, index) in availableItemTypes"/>
                    </draggable>
                </jinya-editor-pane>
                <jinya-editor-pane>
                    <draggable :options="destinationOptions" @add="itemAdded" @change="itemsChange"
                               @end="drag = false" @start="drag = true" class="jinya-form-builder__draggable"
                               v-model="items">
                        <jinya-form-builder-item :enable="enable" :item="item"
                                                 :key="`${item.position}-${index}`" :position="index"
                                                 @edit-done="editSettingsDone" @from-address-changed="resetSubject"
                                                 @subject-changed="resetFromAddress" @toggle-settings="toggleSettings"
                                                 v-for="(item, index) in items"/>
                    </draggable>
                </jinya-editor-pane>
            </jinya-form>
        </jinya-editor>
        <jinya-modal title="static.forms.forms.builder.leave.title" v-if="leaving">
            {{'static.forms.forms.builder.leave.content'|jmessage(form)}}
            <template slot="buttons-left">
                <jinya-modal-button :closes-modal="true" :is-secondary="true"
                                    @click="stay" label="static.forms.forms.builder.leave.cancel"/>
            </template>
            <template slot="buttons-right">
                <jinya-modal-button :closes-modal="true" :is-success="true"
                                    @click="stayAndSaveChanges" label="static.forms.forms.builder.leave.no"/>
                <jinya-modal-button :closes-modal="true" :is-danger="true"
                                    @click="leave" label="static.forms.forms.builder.leave.yes"/>
            </template>
        </jinya-modal>
    </div>
</template>

<script>
  import draggable from 'vuedraggable';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import Translator from '@/framework/i18n/Translator';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaFormBuilderItem from '@/components/Static/Forms/Forms/Builder/Item';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import Timing from '@/framework/Utils/Timing';
  import Routes from '@/router/Routes';
  import JinyaModal from '@/framework/Markup/Modal/Modal';
  import JinyaModalButton from '@/framework/Markup/Modal/ModalButton';

  export default {
    name: 'Builder',
    components: {
      JinyaModalButton,
      JinyaModal,
      JinyaMessage,
      JinyaLoader,
      JinyaFormBuilderItem,
      JinyaForm,
      JinyaEditorPane,
      JinyaEditor,
      draggable,
    },
    beforeRouteLeave(to, from, next) {
      if (this.actions.length > 0) {
        this.leaving = true;
        this.next = next;
      } else {
        next();
      }
    },
    methods: {
      resetFromAddress(position) {
        this.items.forEach((item) => {
          if (item.position !== position) {
            item.options.fromAddress = false;
          }
        });
      },
      resetSubject(position) {
        this.items.forEach((item) => {
          if (item.position !== position) {
            item.options.subject = false;
          }
        });
      },
      async saveChanges() {
        this.state = 'loading';
        this.enable = false;
        try {
          this.message = Translator.message('static.forms.forms.builder.saving', this.form);
          await JinyaRequest.put(`/api/form/${this.form.slug}/items/batch`, this.actions);
          this.state = 'success';
          this.message = Translator.message('static.forms.forms.builder.saved', this.form);
          await Timing.wait();
          this.actions = [];
          this.$router.push(Routes.Static.Forms.Forms.Overview);
        } catch (error) {
          this.message = Translator.message(`static.forms.forms.builder.${error.message}`, this.form);
          this.state = 'error';
          this.enable = true;
        }
      },
      stayAndSaveChanges() {
        this.stay();
        this.saveChanges();
      },
      stay() {
        this.next(false);
        this.leaving = false;
      },
      leave() {
        this.next();
      },
      back() {
        this.$router.push(Routes.Static.Forms.Forms.Overview);
      },
      deleteItem(item) {
        this.actions.push({
          action: 'delete',
          where: item.oldIndex,
        });
      },
      itemAdded(add) {
        const position = add.newIndex;
        const item = this.items[position];
        const clone = JSON.parse(JSON.stringify(item));
        clone.position = position;

        this.items.splice(position, 1, clone);

        this.actions.push({
          action: 'add',
          where: position,
          data: clone,
        });
      },
      itemsChange(data) {
        const { moved } = data;

        if (moved) {
          const newPosition = moved.newIndex;
          const oldPosition = moved.oldIndex;

          this.actions.push({
            action: 'move',
            from: oldPosition,
            to: newPosition,
          });
        }
      },
      editSettingsDone(item) {
        this.actions.push({
          action: 'edit',
          where: item.position,
          data: item.data,
        });
      },
      toggleSettings(item) {
        this.items = this.items.map((elem) => {
          const result = { ...elem };
          if (item.position !== result.position) {
            result.showSettings = false;
          }

          return result;
        });
      },
    },
    computed: {
      originOptions() {
        return {
          handle: '.jinya-form-builder__input',
          disabled: !this.enable,
          group: {
            name: 'edit',
            pull: 'clone',
            put: false,
          },
        };
      },
      destinationOptions() {
        return {
          handle: '.jinya-form-builder__input',
          disabled: !this.enable,
          group: 'edit',
        };
      },
      availableItemTypes() {
        return [
          {
            type: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType',
            label: Translator.message('static.forms.forms.builder.default_label.text_type'),
            spamFilter: [],
            options: {
              required: false,
            },
          },
          {
            type: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\EmailType',
            label: Translator.message('static.forms.forms.builder.default_label.email_type'),
            options: {
              required: false,
            },
          },
          {
            type: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType',
            label: Translator.message('static.forms.forms.builder.default_label.choice_type'),
            options: {
              required: false,
              choices: [],
            },
          },
          {
            type: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\CheckboxType',
            label: Translator.message('static.forms.forms.builder.default_label.checkbox_type'),
            options: {
              required: false,
            },
          },
          {
            type: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextareaType',
            label: Translator.message('static.forms.forms.builder.default_label.textarea_type'),
            spamFilter: [],
            options: {
              required: false,
            },
          },
        ];
      },
    },
    async mounted() {
      this.loading = true;
      const form = await JinyaRequest.get(`/api/form/${this.form.slug}`);
      this.form = form;

      DOMUtils.changeTitle(Translator.message('static.forms.forms.builder.title', form));
      EventBus.$emit(Events.header.change, Translator.message('static.forms.forms.builder.title', form));

      this.items = await JinyaRequest.get(`/api/form/${this.form.slug}/items`);
      this.loading = false;
    },
    data() {
      return {
        form: {
          title: '',
          slug: this.$route.params.slug,
        },
        message: '',
        state: '',
        enable: true,
        items: [],
        loading: false,
        actions: [],
        drag: false,
        leaving: false,
      };
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-form-builder__draggable {
        width: 100%;
        min-height: 2rem;

        &:empty {
            background: $primary-lighter;
            position: relative;
            height: 4rem;

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

    .jinya-form-builder__trash {
        width: 100%;
        display: flex;
        transition: opacity 0.3s;
        background: $negative;
        color: $white;
        justify-content: center;
        flex-direction: row;
        align-items: center;
        min-height: 3rem;

        &:empty {
            position: relative;

            &::before {
                position: absolute;
                content: '\f1c0' attr(data-message);
                font-family: 'Material Design Icons', $font-family;
                color: $white;
                font-size: 1.25rem;
            }
        }
    }
</style>
