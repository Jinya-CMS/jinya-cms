<template>
  <div class="jinya-my-jinya">
    <jinya-message :message="message" :state="state" v-if="state"></jinya-message>
    <div class="jinya-my-jinya__content">
      <aside class="jinya-my-jinya__sidebar">
        <img :src="artist.profilePicture" alt="" class="jinya-my-jinya__profile-picture">
        <button @click="pickProfilePicture()"
                class="jinya-my-jinya__profile-picture jinya-my-jinya__profile-picture--button"
                v-if="editMode"></button>
        <span class="jinya-my-jinya__name" v-if="!editMode">{{artist.artistName}}</span>
        <jinya-input label="my_jinya.my_jinya.artist_name" v-if="editMode" v-model="artist.artistName"/>
        <a :href="`mailto:${artist.email}`" class="jinya-my-jinya__email" v-if="!editMode">{{artist.email}}</a>
        <jinya-input label="my_jinya.my_jinya.email" v-if="editMode" v-model="artist.email"/>
      </aside>
      <div class="jinya-my-jinya__about-me">
        <span class="jinya-my-jinya__about-me-title">{{'my_jinya.my_jinya.about_me'|jmessage}}</span>
        <section class="jinya-my-jinya__about-me-content" v-html="aboutMe" v-if="!editMode"></section>
        <jinya-tiny-mce :content="aboutMe" v-if="editMode" v-model="aboutMe"></jinya-tiny-mce>
        <div class="jinya-my-jinya__buttonbar" v-if="editMode">
          <jinya-button :is-inverse="true" :is-secondary="true" @click="cancel()" label="my_jinya.my_jinya.cancel"/>
          <jinya-button :is-inverse="true" :is-primary="true" @click="save()" label="my_jinya.my_jinya.save"/>
        </div>
      </div>
    </div>
    <jinya-floating-action-button :is-secondary="true" @click="editProfile()" icon="pencil" v-if="!editMode"/>
  </div>
</template>

<script>
  import Routes from '@/router/Routes';
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';
  import { getCurrentUser } from '@/framework/Storage/AuthStorage';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import JinyaTinyMce from '@/framework/Markup/Form/TinyMce';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import Timing from '@/framework/Utils/Timing';
  import { refreshMe } from '@/security/Authentication';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import FileUtils from '@/framework/IO/FileUtils';

  export default {
    name: 'MyJinya',
    components: {
      JinyaInput,
      JinyaMessage,
      JinyaButton,
      JinyaTinyMce,
      JinyaFloatingActionButton,
    },
    async mounted() {
      if (this.isMe) {
        this.artist = getCurrentUser();
        this.aboutMe = await JinyaRequest.get(`/api/user/${this.artist.id}/about`);
      } else {
        this.artist = await JinyaRequest.get(`/api/user/${this.$route.params.id}`);
        this.aboutMe = await JinyaRequest.get(`/api/user/${this.$route.params.id}/about`);
      }


      DOMUtils.changeTitle(this.artist.artistName);
    },
    methods: {
      editProfile() {
        if (this.isMe) {
          this.editMode = true;
        } else {
          this.$router.push({
            name: Routes.Configuration.General.Artists.Edit.name,
            params: {
              id: this.$route.params.id,
            },
          });
        }
      },
      pickProfilePicture() {
        const input = document.createElement('input');
        input.type = 'file';
        input.style.display = 'none';
        input.addEventListener('change', async () => {
          const file = input.files.item(0);
          this.artist.profilePicture = await FileUtils.getAsDataUrl(file);
          this.profilePicture = file;
          input.remove();
        });
        document.body.append(input);
        input.click();
      },
      cancel() {
        this.editMode = false;
        this.artist = getCurrentUser();
        this.state = null;
      },
      async save() {
        let profileSaved = false;
        let profilePictureSaved = false;
        this.state = 'loading';
        this.message = 'my_jinya.my_jinya.profile.data_saving';

        try {
          await JinyaRequest.put('/api/me', {
            email: this.artist.email,
            artistName: this.artist.artistName,
            aboutMe: this.aboutMe,
          });
          profileSaved = true;

          this.message = 'my_jinya.my_jinya.profile.picture_saving';
          await JinyaRequest.upload('/api/me/profilepicture', this.profilePicture);
          profilePictureSaved = true;

          this.state = 'success';
          this.message = 'my_jinya.my_jinya.profile.saved';

          await refreshMe();
          this.artist = getCurrentUser();
          this.editMode = false;

          await Timing.wait(3 * 1000);
          this.state = null;
        } catch {
          this.state = 'error';
          if (!profileSaved) {
            this.message = 'my_jinya.my_jinya.profile.data_not_saved';
          } else if (!profilePictureSaved) {
            this.message = 'my_jinya.my_jinya.profile.picture_not_saved';
          }
        }
      },
    },
    data() {
      const isMe = this.$route.name === Routes.MyJinya.Account.Profile.name;

      return {
        isMe,
        editMode: false,
        aboutMe: '',
        state: null,
        message: '',
        artist: {
          id: -1,
          artistName: '',
          email: '',
          profilePicture: '',
        },
      };
    },
  };
</script>

<style lang="scss">
  .jinya-my-jinya {
    padding-top: 2rem;

    .jinya-message {
      margin-bottom: 2rem;
    }
  }

  .jinya-my-jinya__content {
    display: flex;
  }

  .jinya-my-jinya__sidebar {
    flex: 0 0 30%;
    min-width: 30%;
    display: grid;
    grid-template-columns: auto;
    grid-template-rows: auto auto auto;
    grid-row-gap: 1rem;
    align-self: flex-start;
  }

  .jinya-my-jinya__profile-picture {
    grid-row-start: 1;
    grid-row-end: 2;
    grid-column-start: 1;
    width: 100%;

    &--button {
      background: transparentize(#fff, 0.7);
      border: none;
      position: relative;
      cursor: pointer;
      transition: background-color 0.3s;

      &:hover {
        background: transparentize(#fff, 0.5);
      }

      &::before {
        position: absolute;
        content: '\f3eb';
        font-family: 'Material Design Icons';
        color: white;
      }
    }
  }

  .jinya-my-jinya__name {
    font-size: 2rem;
    width: 100%;
    grid-row-start: 2;
    grid-row-end: 3;
    font-weight: lighter;
  }

  .jinya-my-jinya__email {
    width: 100%;
    grid-row-start: 3;
    grid-row-end: 4;
  }

  .jinya-my-jinya__about-me {
    flex: 0 0 70%;
    min-width: 70%;
    padding-left: 1rem;
    display: flex;
    flex-flow: row wrap;
  }

  .jinya-my-jinya__about-me-title {
    font-size: 2rem;
    font-weight: lighter;
    width: 100%;
  }

  .jinya-my-jinya__about-me-content {
    width: 100%;
    @include content-styling;
  }

  .jinya-my-jinya__buttonbar {
    width: 100%;
    display: flex;
    justify-content: flex-end;
    margin-top: 1rem;

    .jinya-button {
      margin-left: 1rem;
    }
  }
</style>
