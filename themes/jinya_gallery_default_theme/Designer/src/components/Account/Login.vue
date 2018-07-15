<template>
    <div class="jinya-login">
        <jinya-message class="jinya-login__message" :message="message" :state="state" v-if="state"/>
        <jinya-form @submit="submit" class="jinya-login__form">
            <h1 class="jinya-login__title" v-jinya-message="'account.login.title'"
                :class="{'no--margin-top': state}"></h1>
            <jinya-input :autofocus="true" autocomplete="login email" v-model="email" label="account.login.email"
                         key="email" :required="true" type="email" v-if="!twoFactorRequested"/>
            <jinya-input autocomplete="login current-password" v-model="password" label="account.login.password"
                         key="password" :required="true" type="password" v-if="!twoFactorRequested"/>
            <jinya-input v-model="twoFactorCode" label="account.login.2fa_code" key="2fa_code"
                         :required="true" type="text" v-if="twoFactorRequested"/>
            <jinya-button :is-primary="true" label="account.login.submit" type="submit" slot="buttons"/>
        </jinya-form>
    </div>
</template>

<script>
  import JinyaInput from "@/framework/Markup/Form/Input";
  import JinyaButton from "@/framework/Markup/Button";
  import JinyaForm from "@/framework/Markup/Form/Form";
  import JinyaRequest from "@/framework/Ajax/JinyaRequest";
  import Lockr from 'lockr';
  import Routes from "@/router/Routes";
  import JinyaMessage from "@/framework/Markup/Validation/Message";
  import Translator from "@/framework/i18n/Translator";

  export default {
    components: {
      JinyaMessage,
      JinyaForm,
      JinyaButton,
      JinyaInput
    },
    name: "Login",
    data() {
      return {
        email: '',
        password: '',
        message: '',
        twoFactorCode: '',
        twoFactorRequested: false,
        state: ''
      };
    },
    methods: {
      async submit() {
        if (Lockr.get('JinyaDeviceCode') || this.twoFactorRequested) {
          await this.login();
        } else {
          await this.requestTwoFactor();
        }
      },
      async requestTwoFactor() {
        try {
          this.message = Translator.message('account.login.pending');
          this.state = 'loading';

          await JinyaRequest.post('/api/2fa', {
            username: this.email,
            password: this.password
          });
          this.twoFactorRequested = true;

          this.state = "secondary";
          this.message = Translator.message('account.login.two_factor_needed');
        } catch (e) {
          console.error(e);
          this.message = Translator.validator(`account.login.login_failed`);
          this.state = 'error';
        }
      },
      async login() {
        try {
          this.message = Translator.message('account.login.pending');
          this.state = 'loading';

          const headers = {};

          if (Lockr.get('JinyaDeviceCode')) {
            headers['JinyaDeviceCode'] = Lockr.get('JinyaDeviceCode');
          }

          const value = await JinyaRequest.send('POST', '/api/login', {
            username: this.email,
            password: this.password,
            twoFactorCode: this.twoFactorCode
          }, headers);
          Lockr.set('JinyaApiKey', value.apiKey);
          Lockr.set('JinyaDeviceCode', value.deviceCode);

          this.$router.push(Routes.Home.StartPage);
        } catch (e) {
          if (e.type === 'UnknownDeviceException') {
            Lockr.rm('JinyaDeviceCode');
            await this.requestTwoFactor();
          } else {
            this.message = Translator.validator(`account.login.login_failed`);
            this.state = 'error';
          }
        }
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-login {
        height: auto;
        overflow: auto;
        background-color: scale_color($gray-200, $alpha: 80%);
        margin: 10% 35% auto;

        .jinya-login__form {
            padding: 1.5em 3em;
        }

        .jinya-login__title {
            margin: 0.7rem 0;
        }

        .jinya-login__message {
            padding-left: 3em;
            padding-right: 3em;
        }
    }

    .no--margin-top {
        margin-top: 0;
    }
</style>