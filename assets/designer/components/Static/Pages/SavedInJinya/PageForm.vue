<template>
  <jinya-editor>
    <jinya-message :message="validationMessage" :state="validationState" class="is--page-editor" v-if="validationState">
      <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
        <jinya-button :is-danger="true" label="static.pages.page_form.back"
                      to="Static.Pages.SavedInJinya.Overview"/>
        <jinya-button :is-secondary="true" :query="{keyword: $route.params.slug}"
                      label="static.pages.page_form.search" to="Static.Pages.SavedInJinya.Overview"/>
      </jinya-message-action-bar>
    </jinya-message>
    <jinya-form :cancel-label="cancelLabel" :enable="enable" :save-label="saveLabel"
                @back="back" @submit="save" class="jinya-form--page" v-if="!(hideOnError && state === 'error')">
      <jinya-input :enable="enable" :is-static="isStatic" :required="true"
                   :validation-message="'static.pages.page_form.title.empty'|jvalidator"
                   label="static.pages.page_form.title"
                   v-model="page.title"/>
      <jinya-tiny-mce :content="page.content" :required="true" @invalid="contentInvalid" @valid="contentValid"
                      height="400px" v-model="page.content"/>
    </jinya-form>
  </jinya-editor>
</template>

<script>
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaTinyMce from '@/framework/Markup/Form/TinyMce';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import Routes from '@/router/Routes';
  import Translator from '@/framework/i18n/Translator';

  export default {
    name: 'jinya-page-form',
    components: {
      JinyaForm,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaEditorPane,
      JinyaEditor,
      JinyaInput,
      JinyaTinyMce,
      JinyaButton,
    },
    props: {
      message: {
        type: String,
        default() {
          return '';
        },
      },
      state: {
        type: String,
        default() {
          return '';
        },
      },
      isStatic: {
        type: Boolean,
        default() {
          return false;
        },
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
      hideOnError: {
        type: Boolean,
        default() {
          return false;
        },
      },
      saveLabel: {
        type: String,
        default() {
          return 'static.pages.page_form.save';
        },
      },
      cancelLabel: {
        type: String,
        default() {
          return 'static.pages.page_form.back';
        },
      },
      page: {
        type: Object,
        default() {
          return {
            title: '',
            content: '',
          };
        },
      },
    },
    computed: {
      validationMessage() {
        if (this.internalMessage) {
          return this.internalMessage;
        }

        return this.message;
      },
      validationState() {
        if (this.internalState) {
          return this.internalState;
        }

        return this.state;
      },
    },
    data() {
      return {
        internalMessage: null,
        internalState: null,
      };
    },
    methods: {
      contentInvalid() {
        this.internalState = 'error';
        this.internalMessage = Translator.validator('static.pages.page_form.content.empty.on_input');
      },
      contentValid() {
        this.internalState = null;
        this.internalMessage = null;
      },
      back() {
        this.$router.push(Routes.Static.Pages.SavedInJinya.Overview);
      },
      save() {
        if (this.page.content) {
          const page = {
            title: this.page.title,
            content: this.page.content,
          };

          this.$emit('save', page);
        } else {
          this.internalState = 'error';
          this.internalMessage = Translator.validator('static.pages.page_form.content.empty.on_save');
        }
      },
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-page-editor {
    height: 400px;
    flex: 1 1 auto;
  }

  .is--page-editor {
    margin: 0;
  }
</style>
