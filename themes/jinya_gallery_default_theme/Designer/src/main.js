// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from '@/router';
import i18n from '@/i18n';
import { refreshMe } from '@/security/Authentication';
import roles from '@/security/Roles';

Vue.config.productionTip = false;

function startApp() {
  new Vue({
    el: '#app',
    router,
    i18n,
    roles,
    components: { App },
    template: '<App/>',
  });
}

refreshMe().then(startApp).catch(() => {
  startApp();
  // router.push(Routes.Account.Login);
});
