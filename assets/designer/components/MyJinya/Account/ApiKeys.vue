<template>
    <div style="padding-top: 1rem">
        <jinya-loader :loading="loading"/>
        <jinya-message :key="`message-${apiKey.key}`" :message="getKeyMessage(apiKey)"
                       state="info" v-for="apiKey in apiKeys">
            <jinya-message-action-bar :key="`message-action-bar-${apiKey.key}`">
                <jinya-button :isDanger="true" :key="`button-${apiKey.key}`"
                              @click="deleteToken(apiKey.key)" label="my_jinya.account.api_key.delete"/>
            </jinya-message-action-bar>
        </jinya-message>
    </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaButton from '@/framework/Markup/Button';
  import UAParser from 'ua-parser-js';
  import Routes from '@/router/Routes';
  import { getApiKey } from '@/framework/Storage/AuthStorage';

  export default {
    name: 'ApiKeys',
    components: {
      JinyaButton,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaLoader,
    },
    data() {
      return {
        apiKeys: [],
        loading: true,
        message: '',
        state: '',
        invalidateAfter: 0,
      };
    },
    methods: {
      getKeyMessage(apiKey) {
        const dataMessage = Translator.message('my_jinya.account.api_key.device', this.getData(apiKey));
        const invalidatesOnParameters = this.getInvalidatesOn(apiKey.validSince);
        const invalidatesOn = Translator.message('my_jinya.account.api_key.invalidates_on', invalidatesOnParameters);

        return [dataMessage, invalidatesOn].join('<br />');
      },
      getData(apiKey) {
        const parsed = new UAParser(apiKey.userAgent);

        return {
          browser: [parsed.getBrowser().name, parsed.getBrowser().version].join(' '),
          os: [parsed.getOS().name, parsed.getOS().version].join(' '),
          ip: apiKey.remoteAddress,
        };
      },
      getInvalidatesOn(validSince) {
        const invalidatesOn = new Date(validSince);
        invalidatesOn.setSeconds(invalidatesOn.getSeconds() + this.invalidateAfter);

        return {
          time: invalidatesOn.toLocaleTimeString(),
          date: invalidatesOn.toLocaleDateString(),
        };
      },
      async deleteToken(token) {
        await JinyaRequest.delete(`/api/account/api_key/${token}`);
        if (token === getApiKey()) {
          this.$router.push(Routes.Account.Login);
        }
        await this.loadData();
      },
      async loadData() {
        try {
          const response = await JinyaRequest.get('/api/account/api_key');
          this.apiKeys = response.items;
          this.invalidateAfter = response.invalidateApiKeyAfter;
        } catch (e) {
          this.state = 'error';
          this.message = Translator.validator(`my_jinya.account.api_key.${e.message}`);
        }
      },
    },
    async mounted() {
      await this.loadData();
      this.loading = false;
    },
  };
</script>
