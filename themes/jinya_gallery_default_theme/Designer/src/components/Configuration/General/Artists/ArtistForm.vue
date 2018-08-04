<template>
  <jinya-editor>
    <jinya-message :message="message" :state="state" v-if="state">
      <jinya-message-action-bar class="jinya-message__action-bar" v-if="state === 'error'">
        <jinya-button label="configuration.general.artists.artist_form.back" :is-danger="true"
                      to="Configuration.General.Artists.Overview"/>
      </jinya-message-action-bar>
    </jinya-message>
    <jinya-form v-if="!(hideOnError && state === 'error')" @submit="save" class="jinya-form--artist" @back="back"
                :enable="enable" :cancel-label="cancelLabel" :save-label="saveLabel">
      <jinya-editor-pane>
        <jinya-editor-preview-image :src="artist.profilePicture"/>
      </jinya-editor-pane>
      <jinya-editor-pane>
        <jinya-input :is-static="isStatic" :enable="enable" v-model="artist.firstname" class="is--half"
                     label="configuration.general.artists.artist_form.firstname" :required="true"/>
        <jinya-input :is-static="isStatic" :enable="enable" v-model="artist.lastname" class="is--half"
                     label="configuration.general.artists.artist_form.lastname" :required="true"/>
        <jinya-input :is-static="isStatic" :enable="enable" type="email" v-model="artist.email"
                     label="configuration.general.artists.artist_form.email" :required="true"/>
        <jinya-input v-if="showPassword" :enable="enable" type="password" v-model="artist.password"
                     label="configuration.general.artists.artist_form.password" :required="true"/>
        <jinya-file-input v-if="!isStatic" :enable="enable" accept="image/*" @picked="picturePicked"
                          label="configuration.general.artists.artist_form.profile_picture"/>
        <jinya-choice :is-static="isStatic" label="configuration.general.artists.artist_form.activation"
                      :selected="artist.enabled" :enable="enable" :choices="activationOptions"
                      @selected="enableChanged"/>
        <jinya-choice :is-static="isStatic" label="configuration.general.artists.artist_form.roles"
                      :multiple="true"
                      :choices="rolesOptions" :enable="enable" @selected="rolesChanged"
                      :selected="artist.roles"/>
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
          firstname: this.artist.firstname,
          lastname: this.artist.lastname,
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

<style scoped lang="scss">
  .jinya-input,
  .jinya-choice {
    flex: 0 0 100%;

    &.is--half {
      flex-basis: 49%;

      &:nth-child(odd) {
        margin-right: 1%;
      }

      &:nth-child(even) {
        margin-left: 1%;
      }
    }
  }
</style>
