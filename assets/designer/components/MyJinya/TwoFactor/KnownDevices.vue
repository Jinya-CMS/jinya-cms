<template>
  <div style="padding-top: 1rem">
    <jinya-loader :loading="loading"/>
    <jinya-message message="my_jinya.two_factor.known_devices.no_devices" state="info" v-if="tokens.length === 0"/>
    <jinya-message :key="`message-${token.key}`" :params="getData(token)"
                   message="my_jinya.two_factor.known_devices.device"
                   state="info" v-for="token in tokens">
      <jinya-message-action-bar :key="`message-action-bar-${token.key}`">
        <jinya-button :isDanger="true" :key="`button-${token.key}`"
                      @click="deleteToken(token.key)" label="my_jinya.two_factor.known_devices.delete"/>
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

  export default {
    name: 'KnownDevices',
    components: {
      JinyaButton,
      JinyaMessageActionBar,
      JinyaMessage,
      JinyaLoader,
    },
    data() {
      return {
        tokens: [],
        loading: true,
        message: '',
        state: '',
      };
    },
    methods: {
      getData(token) {
        const parsed = new UAParser(token.userAgent);
        return {
          browser: [parsed.getBrowser().name, parsed.getBrowser().version].join(' '),
          os: [parsed.getOS().name, parsed.getOS().version].join(' '),
          ip: token.remoteAddress,
        };
      },
      async deleteToken(token) {
        await JinyaRequest.delete(`/api/account/known_device/${token}`);
        await this.loadData();
      },
      async loadData() {
        try {
          const tokens = await JinyaRequest.get('/api/account/known_device');
          this.tokens = tokens.items;
        } catch (e) {
          this.state = 'error';
          this.message = Translator.validator(`my_jinya.account.two_factor.${e.message}`);
        }
      },
    },
    async mounted() {
      await this.loadData();
      this.loading = false;
    },
  };
</script>

<style scoped>

</style>
