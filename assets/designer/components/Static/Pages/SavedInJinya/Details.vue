<template>
    <jinya-loader :loading="loading" v-if="loading"/>
    <div class="jinya-page" v-else>
        <jinya-message :message="message" :state="state" v-if="state"/>
        <div v-else v-html="page.content"></div>
        <jinya-floating-action-button :to="editLink" icon="pencil"/>
    </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import DOMUtils from '@/framework/Utils/DOMUtils';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';
  import JinyaFloatingActionButton from '@/framework/Markup/FloatingActionButton';
  import Routes from '@/router/Routes';

  export default {
    name: 'PageDetails',
    components: {
      JinyaFloatingActionButton,
      JinyaMessage,
      JinyaLoader,
    },
    data() {
      return {
        state: '',
        message: '',
        loading: false,
        page: {
          title: '',
          content: '',
        },
      };
    },
    computed: {
      editLink() {
        return {
          name: Routes.Static.Pages.SavedInJinya.Edit.name,
          params: {
            slug: this.$route.params.slug,
          },
        };
      },
    },
    async mounted() {
      this.loading = true;
      try {
        this.page = await JinyaRequest.get(`/api/page/${this.$route.params.slug}`);
        DOMUtils.changeTitle(this.page.title);
        EventBus.$emit(Events.header.change, this.page.title);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`static.pages.${error.message}`);
      }
      this.loading = false;
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page {
        height: 100%;

        img {
            max-width: 100%;
        }

        .ql-video {
            width: 100%;
            height: 59%;
        }

        .ql-align-center {
            text-align: center;
        }

        .ql-align-right {
            text-align: right;
        }
    }
</style>
