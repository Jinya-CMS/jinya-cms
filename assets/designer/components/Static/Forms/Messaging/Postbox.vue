<template>
    <div class="jinya-postbox">
        <aside class="jinya-postbox__boxes">
            <jinya-loader :loading="true" v-if="postboxesLoading"/>
            <router-link :key="postbox.name" :to="postbox.href" active-class="is--selected" class="jinya-postbox__box"
                         v-else v-for="postbox in postboxes">
                {{postbox.name}}
            </router-link>
        </aside>
        <div class="jinya-postbox__messages">
            <jinya-loader :loading="true" v-if="messagesLoading"/>
            <template>
                <!-- eslint-disable -->
                <div
                    :class="{'is--selected': selectedMessage !== null && message.id === selectedMessage.id, 'is--unread': !message.read}"
                    :key="message.id" @click="selectMessage(message)" class="jinya-postbox__message"
                    v-for="message in messages">
                    <!-- eslint-enable -->
                    <p class="jinya-postbox__message-first-line">
                        <span class="jinya-postbox__message-subject">{{message.subject}}</span>
                        <span class="jinya-postbox__message-from">{{message.fromAddress}}</span>
                    </p>
                    <span class="jinya-postbox__message-preview-content">{{getTextFromHtml(message.content)}}</span>
                </div>
                <div class="jinya-postbox__message-pager" v-if="totalCount > count">
                    <jinya-icon-button :is-disabled="!control.previous" :is-primary="true" @click="previousPage"
                                       icon="chevron-left"/>
                    <span class="jinya-postbox__message-page">{{currentPage}}</span>
                    <jinya-icon-button :is-disabled="!control.next" :is-primary="true" @click="nextPage"
                                       icon="chevron-right"/>
                </div>
            </template>
        </div>
        <div class="jinya-postbox__read">
            <template v-if="selectedMessage">
                <span class="jinya-postbox__read-subject">{{selectedMessage.subject}}</span>
                <div class="jinya-postbox__read-header">
                    <span class="jinya-postbox__read-from">{{selectedMessage.fromAddress}}</span>
                    <div class="jinya-postbox__read-buttons">
                        <!-- eslint-disable -->
                        <jinya-icon-button
                            :href="`mailto:${selectedMessage.fromAddress}?body=${encodeURI(getTextFromHtml(selectedMessage.content))}&subject=${encodeURI(selectedMessage.subject)}`"
                            :is-primary="true" :title="'static.forms.messages.hover.reply'|jmessage"
                            class="jinya-postbox__read-button" icon="reply" v-if="selectedPostbox.type !== 'spam'"/>
                        <jinya-icon-button
                            :href="`mailto:?body=${encodeURI(getTextFromHtml(selectedMessage.content))}&subject=${encodeURI(selectedMessage.subject)}`"
                            :is-primary="true" :title="'static.forms.messages.hover.forward'|jmessage"
                            class="jinya-postbox__read-button" icon="forward" v-if="selectedPostbox.type !== 'spam'"/>
                        <!-- eslint-enable -->
                        <jinya-icon-button :is-primary="true" :title="'static.forms.messages.hover.spam'|jmessage"
                                           @click="move('spam')" class="jinya-postbox__read-button"
                                           icon="file-document-box-remove-outline"
                                           v-if="selectedPostbox.type === 'inbox' || selectedPostbox.type === 'all'"/>
                        <jinya-icon-button :is-primary="true" :title="'static.forms.messages.hover.archive'|jmessage"
                                           @click="move('archive')" class="jinya-postbox__read-button" icon="archive"
                                           v-if="selectedPostbox.type !== 'archived'"/>
                        <jinya-icon-button :is-primary="true" :title="'static.forms.messages.hover.inbox'|jmessage"
                                           @click="move('inbox')" class="jinya-postbox__read-button" icon="inbox"
                                           v-if="selectedPostbox.type !== 'inbox' && selectedPostbox.type !== 'all'"/>
                        <jinya-icon-button :is-danger="true" :title="'static.forms.messages.hover.trash'|jmessage"
                                           @click="move('trash')" class="jinya-postbox__read-button" icon="trash-can"
                                           v-if="selectedPostbox.type !== 'deleted'"/>
                        <jinya-icon-button :is-danger="true" :title="'static.forms.messages.hover.delete'|jmessage"
                                           @click="deletePermanently" class="jinya-postbox__read-button" icon="close"
                                           v-if="selectedPostbox.type === 'deleted'"/>
                    </div>
                </div>
                <div class="jinya-postbox__read-content" v-html="selectedMessage.content"></div>
            </template>
        </div>
    </div>
</template>

<script>
  import JinyaIconButton from '@/framework/Markup/IconButton';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import Routes from '@/router/Routes';
  import EventBus from '@/framework/Events/EventBus';
  import Events from '@/framework/Events/Events';

  export default {
    name: 'Postbox',
    components: { JinyaLoader, JinyaIconButton },
    async beforeRouteUpdate(to, from, next) {
      if (!to.query.keyword) {
        EventBus.$emit(Events.search.reset);
      }
      EventBus.$emit(Events.search.reset);
      await this.loadData(to);
      next();
    },
    beforeRouteEnter(to, from, next) {
      if (!to.query.keyword) {
        EventBus.$emit(Events.search.reset);
      }
      next(async (vm) => {
        await vm.loadData(to);
      });
    },
    async mounted() {
      this.postboxesLoading = true;
      const forms = await JinyaRequest.get('/api/form');
      const postboxes = [];
      postboxes.push({
        name: Translator.message('static.forms.messages.all_inboxes'),
        type: 'all',
        href: {
          name: Routes.Static.Forms.Messages.Action.name,
          params: {
            action: 'all',
          },
        },
      });

      forms.items.forEach(item => postboxes.push({
        name: `${Translator.message('static.forms.messages.inbox')} (${item.title})`,
        type: 'inbox',
        formSlug: item.slug,
        href: {
          name: Routes.Static.Forms.Messages.Form.name,
          params: {
            slug: item.slug,
            action: 'inbox',
          },
        },
      }));

      postboxes.push({
        name: Translator.message('static.forms.messages.archive'),
        type: 'archived',
        href: {
          name: Routes.Static.Forms.Messages.Action.name,
          params: {
            action: 'archived',
          },
        },
      });
      postboxes.push({
        name: Translator.message('static.forms.messages.trash'),
        type: 'deleted',
        href: {
          name: Routes.Static.Forms.Messages.Action.name,
          params: {
            action: 'deleted',
          },
        },
      });
      postboxes.push({
        name: Translator.message('static.forms.messages.spam'),
        type: 'spam',
        href: {
          name: Routes.Static.Forms.Messages.Action.name,
          params: {
            action: 'spam',
          },
        },
      });

      this.postboxes = postboxes;
      this.postboxesLoading = false;

      await this.loadData(this.$route);

      EventBus.$on(Events.search.triggered, (value) => {
        this.$router.push({
          name: this.$route.name,
          params: this.$route.params,
          query: {
            offset: 0,
            count: this.$route.query.count,
            keyword: value.keyword,
          },
        });
      });
    },
    beforeDestroy() {
      EventBus.$off(Events.search.triggered);
    },
    methods: {
      async loadData(route) {
        const { action, slug } = route.params;
        const routeName = route.name;

        if (routeName === Routes.Static.Forms.Messages.Action.name) {
          const selectedPostbox = this.postboxes.find(item => item.type.toLowerCase() === action.toLowerCase());
          await this.selectPostbox(selectedPostbox, route);
        } else if (routeName === Routes.Static.Forms.Messages.Form.name) {
          const selectedPostbox = this.postboxes
            .find(item => item.type.toLowerCase() === 'inbox' && item.formSlug === slug);
          await this.selectPostbox(selectedPostbox, route);
        } else {
          this.$router.push({
            name: Routes.Static.Forms.Messages.Action.name,
            params: {
              action: 'all',
            },
          });
        }
      },
      async move(action) {
        this.messagesLoading = true;
        switch (action) {
          case 'spam':
            await JinyaRequest.put(`/api/message/${this.selectedMessage.id}/spam`);
            break;
          case 'archive':
            await JinyaRequest.put(`/api/message/${this.selectedMessage.id}/archive`);
            break;
          case 'inbox':
            await JinyaRequest.put(`/api/message/${this.selectedMessage.id}/inbox`);
            break;
          case 'trash':
            await JinyaRequest.put(`/api/message/${this.selectedMessage.id}/trash`);
            break;
          default:
            return;
        }

        await this.page(this.$route.query.offset);
      },
      async deletePermanently() {
        this.messagesLoading = true;
        await JinyaRequest.delete(`/api/message/${this.selectedMessage.id}`);
        await this.page(this.$route.query.offset);
      },
      async previousPage() {
        await this.page((this.currentPage - 1) * this.count - this.count);
      },
      async nextPage() {
        await this.page((this.currentPage - 1) * this.count + this.count);
      },
      async page(offset, route) {
        this.messagesLoading = true;
        let messages;
        let { keyword } = route.query;
        if (!keyword) {
          keyword = '';
        }
        if (route.params.action === 'all') {
          // eslint-disable-next-line max-len
          messages = await JinyaRequest.get(`/api/message?offset=${offset}&count=${this.count}&keyword=${keyword}`);
        } else if (route.params.action === 'spam') {
          // eslint-disable-next-line max-len
          messages = await JinyaRequest.get(`/api/message/spam?offset=${offset}&count=${this.count}&keyword=${keyword}`);
        } else if (route.params.action === 'deleted') {
          // eslint-disable-next-line max-len
          messages = await JinyaRequest.get(`/api/message/deleted?offset=${offset}&count=${this.count}&keyword=${keyword || ''}`);
        } else if (route.params.action === 'archived') {
          // eslint-disable-next-line max-len
          messages = await JinyaRequest.get(`/api/message/archived?offset=${offset}&count=${this.count}&keyword=${keyword}`);
        } else {
          // eslint-disable-next-line max-len
          messages = await JinyaRequest.get(`/api/${route.params.slug}/message?offset=${offset}&count=${this.count}&keyword=${keyword}`);
        }

        this.selectedMessage = null;
        this.messages = messages.items;
        this.totalCount = messages.count;
        this.control = messages.control;
        this.currentPage = offset / this.count + 1;
        this.messagesLoading = false;
      },
      getTextFromHtml(content) {
        const div = document.createElement('div');
        div.innerHTML = content;

        return div.innerText;
      },
      async selectPostbox(postbox, route) {
        this.messagesLoading = true;
        this.currentPage = 1;
        this.selectedPostbox = postbox;
        await this.page(0, route);
      },
      selectMessage(message) {
        this.selectedMessage = message;
        JinyaRequest.put(`/api/message/${message.id}/read`).then(() => {
          message.read = true;
        });
      },
    },
    data() {
      return {
        postboxesLoading: true,
        messagesLoading: true,
        postboxes: [],
        selectedPostbox: [],
        messages: [],
        selectedMessage: null,
        currentPage: 1,
        totalCount: 1,
        control: {
          next: false,
          previous: false,
        },
        count: 15,
      };
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-postbox {
        padding-top: 2rem;
        display: grid;
        grid-template-columns: 15% 25% auto;
        grid-gap: 0.5rem;
    }

    .jinya-postbox__boxes {
        grid-column-start: 1;
        grid-column-end: 2;
        border-right: 1px solid $primary-lighter;
    }

    .jinya-postbox__box {
        cursor: pointer;
        color: $black;
        font-size: 2rem;
        padding-bottom: 1rem;
        display: block;
        text-decoration: none;

        &:hover {
            color: $primary-darker;
        }

        &.is--selected {
            color: $primary;
        }
    }

    .jinya-postbox__messages {
        grid-column-start: 2;
        grid-column-end: 3;
        border-right: 1px solid $primary-lighter;
        padding-right: 0.5rem;
        overflow: auto;
        position: relative;
        height: 90vh
    }

    .jinya-postbox__message {
        cursor: pointer;
        border-left: 0.25rem solid transparent;
        box-sizing: border-box;
        padding: 0.25rem 0 0.25rem 0.25rem;

        &.is--selected {
            background: $primary-lighter;
        }

        &.is--unread {
            border-left-color: $primary;
        }

        &:hover {
            background: $primary-lighter;
        }
    }

    .jinya-postbox__message-first-line {
        margin: 0;
        padding: 0 0 0.25rem;
    }

    .jinya-postbox__message-subject {
        width: 50%;
        padding-right: 0.5rem;
        font-size: 1.5rem;
        overflow: hidden;
        text-overflow: ellipsis;
        color: $primary;
        white-space: nowrap;
        display: block;
    }

    .jinya-postbox__message-from {
        width: 50%;
        padding-right: 0.5rem;
        font-size: 1rem;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        display: block;
    }

    .jinya-postbox__message-preview-content {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
    }

    .jinya-postbox__message-pager {
        margin-top: 1rem;
        width: 100%;
        display: flex;
        justify-content: space-between;
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        background: $primary-lighter;
        align-items: baseline;
    }

    .jinya-postbox__read {
        grid-column-start: 3;
        grid-column-end: 4;
        padding-left: 0.5rem;
    }

    .jinya-postbox__read-subject {
        display: block;
        font-size: 1.5rem;
        color: $primary;
    }

    .jinya-postbox__read-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid $primary-lighter;
    }

    .jinya-postbox__read-from {
        justify-self: left;
    }

    .jinya-postbox__read-buttons {
        display: flex;
        justify-self: right;
    }

    .jinya-postbox__read-button {
        font-size: 1.5rem;
    }

    .jinya-postbox__read-content {
        margin-top: 1rem;
        overflow: auto;
        max-height: 80vh;
        white-space: pre-wrap;
    }
</style>
