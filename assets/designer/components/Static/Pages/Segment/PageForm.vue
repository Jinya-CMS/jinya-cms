<template>
  <jinya-editor>
    <jinya-message :message="validationMessage" :state="validationState" class="is--page-editor" v-if="validationState">
      <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
        <jinya-button :is-danger="true" label="static.pages.segment.page_form.back"
                      to="Static.Pages.Segment.Overview"/>
        <jinya-button :is-secondary="true" :query="{keyword: $route.params.slug}"
                      label="static.pages.page_form.search" to="Static.Pages.Segment.Overview"/>
      </jinya-message-action-bar>
    </jinya-message>
    <jinya-form :cancel-label="cancelLabel" :enable="enable" :save-label="saveLabel"
                @back="back" @submit="save" class="jinya-form--page" v-if="!(hideOnError && state === 'error')">
      <jinya-input :enable="enable" :is-static="isStatic" :required="true"
                   :validation-message="'static.pages.segment.page_form.title.empty'|jvalidator"
                   label="static.pages.segment.page_form.name"
                   v-model="page.name"/>
    </jinya-form>
  </jinya-editor>
</template>

<script>
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import Routes from '@/router/Routes';

  export default {
    name: 'jinya-page-form',
    components: {
      JinyaForm,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaEditor,
      JinyaInput,
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
          return 'static.pages.segment.page_form.save';
        },
      },
      cancelLabel: {
        type: String,
        default() {
          return 'static.pages.segment.page_form.back';
        },
      },
      page: {
        type: Object,
        default() {
          return {
            name: '',
          };
        },
      },
    },
    computed: {
      validationMessage() {
        return this.message;
      },
      validationState() {
        return this.state;
      },
    },
    methods: {
      back() {
        this.$router.push(Routes.Static.Pages.Segment.Overview);
      },
      save() {
        const page = {
          name: this.page.name,
        };

        this.$emit('save', page);
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
