<template>
    <div class="jinya-form-builder">
        <jinya-loader :loading="loading"/>
        <jinya-editor v-if="!loading">
            <jinya-form save-label="static.forms.forms.builder.save" cancel-label="static.forms.forms.builder.cancel"
                        @submit="saveChanges" novalidate class="jinya-form-builder__form" @back="back"
                        button-bar-padding-right="0.5rem">
                <draggable @add="deleteItem" v-show="drag" class="jinya-form-builder__trash"
                           :options="destinationOptions">
                    <i class="mdi mdi-delete is--big"></i>
                    <span>{{'static.forms.forms.builder.delete'|jmessage}}</span>
                </draggable>
                <jinya-message :message="message" :state="state"/>
                <jinya-editor-pane>
                    <draggable class="jinya-form-builder__draggable" :options="originOptions"
                               v-model="availableItemTypes">
                        <jinya-form-builder-item v-for="(item, index) in availableItemTypes" :settings-available="false"
                                                 :enable="enable" :item="item" :key="`${item.type}-${index}`"/>
                    </draggable>
                </jinya-editor-pane>
                <jinya-editor-pane>
                    <draggable @change="itemsChange" class="jinya-form-builder__draggable" :options="destinationOptions"
                               v-model="items" @add="itemAdded" @start="drag = true" @end="drag = false">
                        <jinya-form-builder-item v-for="(item, index) in items" :key="`${item.position}-${index}`"
                                                 :item="item" :enable="enable" @toggle-settings="toggleSettings"
                                                 :position="index" @edit-done="editSettingsDone"/>
                    </draggable>
                </jinya-editor-pane>
            </jinya-form>
        </jinya-editor>
        <jinya-modal title="static.forms.forms.builder.leave.title" v-if="leaving">
            {{'static.forms.forms.builder.leave.content'|jmessage(form)}}
            <template slot="buttons-left">
                <jinya-modal-button label="static.forms.forms.builder.leave.cancel" :closes-modal="true"
                                    @click="stay" :is-secondary="true"/>
            </template>
            <template slot="buttons-right">
                <jinya-modal-button label="static.forms.forms.builder.leave.no" :closes-modal="true"
                                    @click="stayAndSaveChanges" :is-success="true"/>
                <jinya-modal-button label="static.forms.forms.builder.leave.yes" :closes-modal="true"
                                    @click="leave" :is-danger="true"/>
            </template>
        </jinya-modal>
    </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import Translator from '@/framework/i18n/Translator';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaFormBuilderTextType from '@/components/Static/Forms/Forms/Builder/TextType';
  import JinyaFormBuilderEmailType from '@/components/Static/Forms/Forms/Builder/EmailType';
  import JinyaFormBuilderCheckboxType from '@/components/Static/Forms/Forms/Builder/CheckboxType';
  import JinyaFormBuilderChoiceType from '@/components/Static/Forms/Forms/Builder/ChoiceType';
  import JinyaFormBuilderTextAreaType from '@/components/Static/Forms/Forms/Builder/TextAreaType';
  import draggable from 'vuedraggable';
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
      JinyaFormBuilderTextAreaType,
      JinyaFormBuilderChoiceType,
      JinyaFormBuilderCheckboxType,
      JinyaFormBuilderEmailType,
      JinyaFormBuilderTextType,
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
          handle: '.jinya-form-builder__component',
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
          handle: '.jinya-form-builder__component',
          disabled: !this.enable,
          group: 'edit',
        };
      },
      availableItemTypes() {
        return [
          {
            type: 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextType',
            label: Translator.message('static.forms.forms.builder.default_label.text_type'),
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

<style scoped lang="scss">
    .jinya-form-builder__draggable {
        width: 100%;
    }

    .jinya-form-builder__trash {
        width: 100%;
        display: flex;
        transition: opacity 0.3s;
        color: $danger;
        justify-content: center;
        flex-direction: row;
        align-items: center;
    }

    .is--big {
        font-size: 250%;
    }
</style>
