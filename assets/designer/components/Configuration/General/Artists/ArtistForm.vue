<template>
    <jinya-editor>
        <jinya-message :message="message" :state="state" v-if="state">
            <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
                <jinya-button :is-danger="true" label="configuration.general.artists.artist_form.back"
                              to="Configuration.General.Artists.Overview"/>
            </jinya-message-action-bar>
        </jinya-message>
        <jinya-form :cancel-label="cancelLabel" :enable="enable" :save-label="saveLabel" @back="back"
                    @submit="save" class="jinya-form--artist" v-if="!(hideOnError && state === 'error')">
            <jinya-editor-pane>
                <jinya-editor-preview-image :src="artist.profilePicture"/>
            </jinya-editor-pane>
            <jinya-editor-pane>
                <jinya-input :enable="enable" :is-static="isStatic" :required="true"
                             :validation-message="'configuration.general.artists.artist_form.artist_name.empty'|jvalidator"
                             label="configuration.general.artists.artist_form.artist_name"
                             v-model="artist.artistName"/>
                <jinya-input :enable="enable" :is-static="isStatic" :required="true"
                             :validation-message="emailValidationMessage|jvalidator"
                             @invalid="emailTypeMismatch = $event.typeMismatch"
                             label="configuration.general.artists.artist_form.email"
                             type="email"
                             v-model="artist.email"/>
                <jinya-input :enable="enable" :required="true"
                             :validation-message="'configuration.general.artists.artist_form.password.empty'|jvalidator"
                             label="configuration.general.artists.artist_form.password"
                             type="password" v-if="showPassword"
                             v-model="artist.password"/>
                <jinya-file-input :enable="enable" @picked="picturePicked" accept="image/*"
                                  label="configuration.general.artists.artist_form.profile_picture"
                                  v-if="!isStatic"/>
                <jinya-choice :choices="activationOptions" :enable="enable"
                              :is-static="isStatic" :selected="artist.enabled" @selected="enableChanged"
                              label="configuration.general.artists.artist_form.activation"/>
                <jinya-choice :choices="rolesOptions" :enable="enable"
                              :is-static="isStatic"
                              :multiple="true" :selected="artist.roles" @selected="rolesChanged"
                              label="configuration.general.artists.artist_form.roles"/>
            </jinya-editor-pane>
        </jinya-form>
    </jinya-editor>
</template>

<script>
  import JinyaEditor from '@/framework/Markup/Form/Editor';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import JinyaEditorPane from '@/framework/Markup/Form/EditorPane';
  import JinyaEditorPreviewImage from '@/framework/Markup/Form/EditorPreviewImage';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaFileInput from '@/framework/Markup/Form/FileInput';
  import JinyaChoice from '@/framework/Markup/Form/Choice';
  import Translator from '@/framework/i18n/Translator';
  import FileUtils from '@/framework/IO/FileUtils';
  import Routes from '@/router/Routes';

  export default {
    name: 'jinya-artist-form',
    components: {
      JinyaChoice,
      JinyaFileInput,
      JinyaInput,
      JinyaEditorPreviewImage,
      JinyaEditorPane,
      JinyaForm,
      JinyaButton,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaEditor,
    },
    props: {
      message: {
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
      state: {
        type: String,
        default() {
          return '';
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
          return 'configuration.general.artists.artist_form.save';
        },
      },
      cancelLabel: {
        type: String,
        default() {
          return 'configuration.general.artists.artist_form.back';
        },
      },
      showPassword: {
        type: Boolean,
        default() {
          return false;
        },
      },
      artist: {
        type: Object,
        default() {
          return {
            profilePicture: '',
            firstname: '',
            lastname: '',
            email: '',
            enabled: {
              text: '',
              value: '',
            },
            roles: [],
          };
        },
      },
    },
    computed: {
      activationOptions() {
        return [
          {
            value: true,
            text: Translator.message('configuration.general.artists.artist_form.enabled'),
          },
          {
            value: false,
            text: Translator.message('configuration.general.artists.artist_form.disabled'),
          },
        ];
      },
      rolesOptions() {
        return window.messages.authentication.roles;
      },
      emailValidationMessage() {
        if (this.emailTypeMismatch) {
          return 'configuration.general.artists.artist_form.email.invalid';
        }

        return 'configuration.general.artists.artist_form.email.empty';
      },
    },
    data() {
      return { emailTypeMismatch: false };
    },
    methods: {
      async picturePicked(files) {
        const file = files.item(0);

        this.artist.profilePicture = await FileUtils.getAsDataUrl(file);
        this.artist.uploadedFile = file;
      },
      save() {
        const artist = {
          profilePicture: this.artist.uploadedFile,
          artistName: this.artist.artistName,
          email: this.artist.email,
          enabled: this.artist.enabled.value,
          roles: this.artist.roles.map(role => role.value),
        };

        if (this.showPassword) {
          artist.password = this.artist.password;
        }

        this.$emit('save', artist);
      },
      enableChanged(choice) {
        const value = choice.value === 'true';

        [this.artist.enabled] = this.activationOptions.filter(option => option.value === value);
      },
      rolesChanged(choice) {
        const idx = this.artist.roles.findIndex(role => role.value === choice.value);

        if (idx > -1) {
          this.artist.roles.splice(idx, 1);
        } else {
          this.artist.roles.push(this.rolesOptions.filter(role => role.value === choice.value)[0]);
        }
      },
      back() {
        this.$router.push(Routes.Configuration.General.Artists.Overview);
      },
    },
  };
</script>
