// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from '@/router';
import i18n from '@/i18n';
import { refreshMe } from '@/security/Authentication';
import roles from '@/security/Roles';
import Routes from '@/router/Routes';
import '@/scss/materialdesignicons.scss';
import '@/scss/roboto-fontface.scss';

Vue.config.productionTip = false;

function startApp() {
  // eslint-disable-next-line no-new
  new Vue({
    el: '#app',
    router,
    i18n,
    roles,
    components: { App },
    template: '<App/>',
  });
}

(async () => {
  try {
    await refreshMe();
    startApp();
  } catch (e) {
    startApp();
    router.push(Routes.Account.Login);
  }
})();
