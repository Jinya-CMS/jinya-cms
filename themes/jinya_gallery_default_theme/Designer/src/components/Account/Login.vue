<template>
    <div class="jinya-login">
        <jinya-message class="jinya-login__message" :message="message" :state="state" v-if="state"/>
        <jinya-form class="jinya-login__form" @submit="login">
            <h1 class="jinya-login__title" v-jinya-message="'account.login.title'"
                :class="{'no--margin-top': state}"></h1>
            <jinya-input :autofocus="true" autocomplete="login email" v-model="email" label="account.login.email"
                         :required="true" type="email"/>
            <jinya-input autocomplete="login current-password" v-model="password" label="account.login.password"
                         :required="true" type="password"/>
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
        state: ''
      };
    },
    methods: {
      async login() {
        try {
          this.message = Translator.message('account.login.pending');
          this.state = 'loading';

          const value = await JinyaRequest.post('/api/login', {username: this.email, password: this.password});
          Lockr.set('JinyaApiKey', value.apiKey);

          this.$router.push(Routes.Home.StartPage);
        } catch (e) {
          this.message = Translator.validator(`account.login.${e.message}`);
          this.state = 'error';
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