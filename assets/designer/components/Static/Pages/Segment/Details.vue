<template>
    <div class="jinya-page">
        <jinya-message :message="message" :state="state" v-if="state"/>
        <jinya-loader :loading="loading" v-if="loading"/>
        <div :key="segment.id" v-else v-for="segment in page.segments">
            <div v-html="segment.html" v-if="segment.html"></div>
            <div v-else>
                <span class="jinya-page-item__heading">{{segment.name}}</span>
                <span class="jinya-page-item__heading">
          <b>{{'static.pages.segment.details.action.action'}}</b>: {{segment.action}}
        </span>
                <span class="jinya-page-item__heading" v-if="segment.action === 'script'">
          <b>{{'static.pages.segment.details.action.script'}}</b>:<br/>
          <monaco-editor :options="{readOnly: true}" :value="segment.script" language="javascript"/>
        </span>
                <span class="jinya-page-item__heading" v-if="segment.action === 'link'">
          <b>{{'static.pages.segment.details.action.target'}}</b>: {{segment.target}}</span>
            </div>
            <hr/>
        </div>
        <jinya-floating-action-button :to="editLink" icon="pencil"/>
    </div>
</template>

<script>
  import MonacoEditor from 'vue-monaco';
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
      MonacoEditor,
    },
    data() {
      return {
        state: '',
        message: '',
        loading: false,
        page: {
          name: '',
          segments: [],
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
        this.page = await JinyaRequest.get(`/api/segment_page/${this.$route.params.slug}`);
        this.enable = true;
        DOMUtils.changeTitle(this.page.name);
        EventBus.$emit(Events.header.change, this.page.name);
      } catch (error) {
        this.state = 'error';
        this.message = Translator.validator(error.message);
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
    }
</style>
