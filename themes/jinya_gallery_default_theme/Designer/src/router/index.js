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
import Events from "../components/Framework/Events/Events";

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
  const apiKey = Lockr.get('JinyaApiKey');
  if (!apiKey && to.name !== Routes.Account.Login.name) {
    next(Routes.Account.Login.route);
  } else {
    EventBus.$emit(Events.navigation.navigated);
    next();
  }
});

export default router;