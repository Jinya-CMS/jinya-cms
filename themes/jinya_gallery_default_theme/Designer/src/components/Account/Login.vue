<template>
    <section class="jinya-login">
        <jinya-form class="jinya-login__form" @submit="login">
            <h1 v-jinya-message="'account.login.title'"></h1>
            <jinya-input :autofocus="true" autocomplete="login email" v-model="email" label="account.login.email"
                         :required="true" type="email"/>
            <jinya-input autocomplete="login current-password" v-model="password" label="account.login.password"
                         :required="true" type="password"/>
            <jinya-button :is-primary="true" label="account.login.submit" type="submit" slot="buttons"/>
        </jinya-form>
    </section>
</template>

<script>
  import JinyaInput from "@/components/Framework/Markup/Form/Input";
  import JinyaButton from "@/components/Framework/Markup/Button";
  import JinyaForm from "@/components/Framework/Markup/Form/Form";
  import JinyaRequest from "@/components/Framework/Ajax/JinyaRequest";
  import Lockr from 'lockr';
  import Routes from "../../router/Routes";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaForm,
      JinyaButton,
      JinyaInput
    },
    name: "login",
    data() {
      return {options: window.options, email: null, password: null};
    },
    methods: {
      login() {
        JinyaRequest.post('/api/login', {username: this.email, password: this.password}).then(value => {
          Lockr.set('JinyaApiKey', value.apiKey);
          this.$router.push({name: Routes.Home.StartPage.name});
        }).catch(reason => alert(reason.message));
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-login {
        height: 100%;
        width: 100%;
        position: relative;
        overflow: auto;

        .jinya-login__background {
            z-index: -1;
            height: 100%;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
        }
        .jinya-login__form {
            margin: 10% 35% auto;
            padding: 1.5em 3em;
            height: auto;
            background-color: transparentize($gray-200, 0.2);

            @include breakpoint-ultra-mobile {
                margin: 0;
            }

            @include breakpoint-tablet-landscape {
                margin-left: 30%;
                margin-right: 30%;
            }

            @include breakpoint-tablet-portrait {
                margin-left: 20%;
                margin-right: 20%;
            }

            @include breakpoint-mobile {
                margin-left: 10%;
                margin-right: 10%;
            }
        }
    }
</style>