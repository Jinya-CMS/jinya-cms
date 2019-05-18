<template>
  <div class="jinya-my-jinya">
    <aside class="jinya-my-jinya__sidebar">
      <img :src="artist.profilePicture" alt="" class="jinya-my-jinya__profile-picture">
      <span class="jinya-my-jinya__name" v-if="!editMode">{{artist.artistName}}</span>
      <a :href="`mailto:${artist.email}`" class="jinya-my-jinya__email" v-if="!editMode">{{artist.email}}</a>
    </aside>
    <div class="jinya-my-jinya__about-me">
      <span class="jinya-my-jinya__about-me-title">{{'my_jinya.my_jinya.about_me'|jmessage}}</span>
      <section class="jinya-my-jinya__about-me-content" v-html="aboutMe" v-if="!editMode"></section>
    </div>
    <jinya-floating-action-button @click="editMode = true" icon="pencil" v-if="!editMode && isMe"/>
  </div>
</template>

<script>
  import Routes from '@/router/Routes';
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';
  import { getCurrentUser } from '@/framework/Storage/AuthStorage';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import DOMUtils from '@/framework/Utils/DOMUtils';

  export default {
    name: 'MyJinya',
    components: {
      JinyaFloatingActionButton,
    },
    async mounted() {
      if (this.isMe) {
        this.artist = getCurrentUser();
      } else {
        this.artist = await JinyaRequest.get(`/api/user/${this.$route.params.id}`);
      }

      this.aboutMe = await JinyaRequest.get(`/api/user/${this.artist.id}/about`);

      DOMUtils.changeTitle(this.artist.artistName);
    },
    data() {
      const isMe = this.$route.name === Routes.MyJinya.Account.Profile.name;

      return {
        isMe,
        editMode: false,
        aboutMe: '',
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
    display: flex;
    margin-top: 2rem;
  }

  .jinya-my-jinya__sidebar {
    flex: 0 0 30%;
    min-width: 30%;
    padding-right: 1rem;
    display: flex;
    flex-flow: row wrap;
    align-content: flex-start;
  }

  .jinya-my-jinya__name {
    font-size: 2rem;
    font-weight: lighter;
    width: 100%;
  }

  .jinya-my-jinya__email {
    width: 100%;
  }

  .jinya-my-jinya__profile-picture {
    margin-bottom: 2rem;
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
</style>
