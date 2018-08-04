<template>
    <jinya-editor>
        <jinya-message :message="message" :state="state" v-if="state">
            <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
                <jinya-button label="configuration.frontend.menus.menu_form.back" :is-danger="true"
                              to="Configuration.Frontend.Menu.Overview"/>
            </jinya-message-action-bar>
        </jinya-message>
        <jinya-form @submit="save" save-label="configuration.frontend.menus.menu_form.save"
                    @back="$emit('back')" cancel-label="configuration.frontend.menus.menu_form.back">
            <jinya-editor-pane>
                <jinya-editor-preview-image :src="menu.logo"/>
            </jinya-editor-pane>
            <jinya-editor-pane>
                <jinya-input label="configuration.frontend.menus.menu_form.name" v-model="menu.name" :enable="enable"
                             :value="menu.name" :required="true"
                             :validation-message="'configuration.frontend.menus.menu_form.name.empty'|jvalidator"/>
                <jinya-file-input label="configuration.frontend.menus.menu_form.logo" @picked="picked" accept="image/*"
                                  :has-value="!!menu.logo" :enable="enable"/>
            </jinya-editor-pane>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaEditorPreviewImage from '@/framework/Markup/Form/EditorPreviewImage';
  import JinyaFileInput from '@/framework/Markup/Form/FileInput';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import FileUtils from '@/framework/IO/FileUtils';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaButton from '@/framework/Markup/Button';

  export default {
    name: 'jinya-menu-form',
    components: {
      JinyaButton,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaForm,
      JinyaInput,
      JinyaFileInput,
      JinyaEditorPreviewImage,
      JinyaEditorPane,
      JinyaEditor,
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
      menu: {
        type: Object,
        default() {
          return {
            name: '',
            logo: '',
          };
        },
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
    },
    methods: {
      async picked(files) {
        const logo = files[0];

        this.menu.pickedLogo = logo;
        this.menu.logo = await FileUtils.getAsDataUrl(logo);
      },
      save() {
        this.$emit('save', {
          logo: this.menu.pickedLogo,
          name: this.menu.name,
        });
      },
    },
  };
</script>
