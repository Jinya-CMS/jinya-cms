<template>
    <jinya-editor>
        <jinya-message :message="message" :state="state" v-if="state">
            <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
                <jinya-button label="static.pages.page_form.back" to="Static.Pages.SavedInJinya.Overview"
                              :is-danger="true"/>
                <jinya-button label="static.pages.page_form.search" to="Static.Pages.SavedInJinya.Overview"
                              :query="{keyword: $route.params.slug}" :is-secondary="true"/>
            </jinya-message-action-bar>
        </jinya-message>
        <jinya-form v-if="!(hideOnError && state === 'error')" @submit="save" class="jinya-form--page"
                    @back="back" :enable="enable" :cancel-label="cancelLabel" :save-label="saveLabel">
            <jinya-input v-model="page.title" label="static.pages.page_form.title" :static="static" :enable="enable"
                         @change="titleChanged"/>
            <jinya-input v-model="page.slug" label="static.pages.page_form.slug" :static="static" :enable="enable"
                         @change="slugChanged"/>
            <jinya-tiny-mce v-model="page.content" :content="page.content" height="400px"/>
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
  import slugify from 'slugify';

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
      static: {
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
            slug: '',
            content: '',
          };
        },
      },
      slugifyEnabled: {
        type: Boolean,
        default() {
          return true;
        },
      },
    },
    methods: {
      back() {
        this.$router.push(Routes.Static.Pages.SavedInJinya.Overview);
      },
      titleChanged(value) {
        if (this.slugifyEnabled) {
          this.page.slug = slugify(value);
        }
      },
      slugChanged(value) {
        this.slugifyEnabled = false;
        this.page.slug = slugify(value);
      },
      save() {
        const page = {
          title: this.page.title,
          slug: this.page.slug,
          content: this.page.content,
        };

        this.$emit('save', page);
      },
    },
  };
</script>

<style scoped lang="scss">
    .jinya-page-editor {
        height: 400px;
        flex: 1 1 auto;
    }
</style>
