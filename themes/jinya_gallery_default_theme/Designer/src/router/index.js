import Vue from 'vue';
import Router from 'vue-router';
import Lockr from 'lockr';
import Routes from "./Routes";
import EventBus from "../components/Framework/Events/EventBus";

import Account from './account';
import Art from './art';
import Static from './static';
import Home from './home';
import Configuration from './configuration';
import Maintenance from './maintenance';
import MyJinya from './myjinya';

const routes = Home
  .concat(Account)
  .concat(Art)
  .concat(Static)
  .concat(Configuration)
  .concat(Maintenance)
  .concat(MyJinya);

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