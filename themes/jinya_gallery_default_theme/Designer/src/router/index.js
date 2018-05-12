import Vue from 'vue';
import Router from 'vue-router';
import Lockr from 'lockr';
import Routes from "./Routes";
import EventBus from "@/framework/Events/EventBus";

import Account from './account';
import Art from './art';
import Static from './static';
import Home from './home';
import Configuration from './configuration';
import Maintenance from './maintenance';
import MyJinya from './myjinya';
import Events from "@/framework/Events/Events";
import Translator from "@/framework/i18n/Translator";
import DOMUtils from "@/framework/Utils/DOMUtils";
import JinyaRequest from "@/framework/Ajax/JinyaRequest";

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

router.beforeEach(async (to, from, next) => {
  const apiKey = Lockr.get('JinyaApiKey');
  if (!apiKey && to.name !== Routes.Account.Login.name) {
    next(Routes.Account.Login.route);
  } else {
    try {
      if (to.name !== Routes.Account.Login.name) {
        await JinyaRequest.head('/api/login');
      }

      EventBus.$emit(Events.navigation.navigating);
      DOMUtils.changeTitle(to.meta && to.meta.title ? Translator.message(to.meta.title) : '');
      next();
    } catch (e) {
      // Lockr.rm('JinyaApiKey');
      next(Routes.Account.Login.route);
    }
  }
});

router.afterEach(() => EventBus.$emit(Events.navigation.navigated));

export default router;