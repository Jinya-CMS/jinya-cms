<template>
    <div class="jinya-login__container">
        <div class="jinya-login">
            <jinya-message :message="message" :state="state" class="jinya-login__message" v-if="state"/>
            <jinya-form @submit="submit" class="jinya-login__form">
                <h1 :class="{'no--margin-top': state}" class="jinya-login__title"
                    v-jinya-message="'account.login.title'"></h1>
                <jinya-input :autofocus="true" :required="true" autocomplete="login email" key="email"
                             label="account.login.email" type="email" v-if="!twoFactorRequested" v-model="email"/>
                <jinya-input :required="true" autocomplete="login current-password" key="password"
                             label="account.login.password" type="password" v-if="!twoFactorRequested"
                             v-model="password"/>
                <jinya-input :required="true" key="2fa_code" label="account.login.2fa_code"
                             type="text" v-if="twoFactorRequested" v-model="twoFactorCode"/>
                <jinya-button :is-primary="true" label="account.login.submit" slot="buttons" type="submit"/>
            </jinya-form>
        </div>
    </div>
</template>

<script>
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaForm from '@/framework/Markup/Form/Form';
  import Routes from '@/router/Routes';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import Translator from '@/framework/i18n/Translator';
  import { login } from '@/security/Authentication';
  import { clearAuth } from '@/framework/Storage/AuthStorage';

  export default {
    components: {
      JinyaMessage,
      JinyaForm,
      JinyaButton,
      JinyaInput,
    },
    name: 'Login',
    data() {
      return {
        email: '',
        password: '',
        message: '',
        twoFactorCode: '',
        twoFactorRequested: false,
        state: '',
      };
    },
    methods: {
      async submit() {
        try {
          this.message = Translator.message('account.login.pending');
          this.state = 'loading';
          clearAuth(false);

          const loginResult = await login(this.email, this.password, this.twoFactorCode);
          if (loginResult) {
            this.$router.push(Routes.Home.StartPage);
          } else {
            this.twoFactorRequested = true;
          }

          this.state = 'secondary';
          this.message = Translator.message('account.login.two_factor_needed');
        } catch (e) {
          this.message = Translator.validator('account.login.login_failed');
          this.state = 'error';
        }
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-app {
        padding-top: 10%;
    }

    .jinya-login__container {
        margin-left: 35%;
        margin-right: 35%;
        padding-top: 10%;
    }

    .jinya-login {
        height: auto;
        overflow: auto;
        background-color: transparentize($gray-200, 0.44);

        .jinya-login__form {
            padding: 1.5em 3em;
        }

        .jinya-login__title {
            margin: 0.7rem 0;
        }

        .jinya-login__message {
            padding-left: 3em;
            padding-right: 3em;
            margin: 0;
        }
    }

    .no--margin-top {
        margin-top: 0;
    }
</style>
