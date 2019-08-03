<template>
    <div class="jinya-page">
        <jinya-message :message="message" :state="state" v-if="state"/>
        <jinya-loader :loading="loading" v-if="loading"/>
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
      this.enable = false;
      try {
        this.page = await JinyaRequest.get(`/api/page/${this.$route.params.slug}`);
        this.enable = true;
        DOMUtils.changeTitle(this.page.title);
        EventBus.$emit(Events.header.change, this.page.title);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(`static.pages.${error.message}`);
      }
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-page {
        font-family: $page-font-family;
        height: 100%;

        h1 {
            font-family: $page-font-family-heading1;
        }

        h2 {
            font-family: $page-font-family-heading2;
        }

        h3 {
            font-family: $page-font-family-heading3;
        }

        h4 {
            font-family: $page-font-family-heading4;
        }

        h5 {
            font-family: $page-font-family-heading5;
        }

        h6 {
            font-family: $page-font-family-heading6;
        }

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
