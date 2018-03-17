import Vue from 'vue'
import Router from 'vue-router'
import Lockr from 'lockr';
import Routes from "./Routes";
import EventBus from "../components/Framework/Events/EventBus";

import Account from './account'
import Art from './art'

const routes = Account.concat(Art);

Vue.use(Router);

const router = new Router({
  mode: 'history',
  routes: routes
});

router.beforeEach((to, from, next) => {
  if (!Lockr.get('JinyaApiKey') && to.name !== Routes.Account.Login.name) {
    next(Routes.Account.Login.route);
  } else {
    EventBus.$emit('navigated');
    next();
  }
});

export default router;