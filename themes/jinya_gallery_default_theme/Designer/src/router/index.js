import Vue from 'vue';
import Router from 'vue-router';
import Lockr from 'lockr';
import Routes from "@/router/Routes";
import EventBus from "@/framework/Events/EventBus";

import Account from './account';
import Art from '@/router/art';
import Static from '@/router/static';
import Home from '@/router/home';
import Configuration from '@/router/configuration';
import Maintenance from '@/router/maintenance';
import MyJinya from '@/router/myjinya';
import Error from '@/router/error';
import Events from "@/framework/Events/Events";
import Translator from "@/framework/i18n/Translator";
import DOMUtils from "@/framework/Utils/DOMUtils";
import {getRoles} from "@/security/CurrentUser";

const routes = Home
  .concat(Account)
  .concat(Art)
  .concat(Static)
  .concat(Configuration)
  .concat(Maintenance)
  .concat(MyJinya)
  .concat(Error);

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
      to.meta.me = {
        roles: getRoles()
      };

      if (to.meta.role && !to.meta.me.roles.includes(to.meta.role)) {
        next(Routes.Error.NotAllowed.route);
      } else {
        EventBus.$emit(Events.navigation.navigating);
        DOMUtils.changeTitle(to.meta && to.meta.title ? Translator.message(to.meta.title) : '');
        next();
      }
    } catch (e) {
      Lockr.rm('JinyaApiKey');
      next(Routes.Account.Login.route);
    }
  }
});

router.afterEach(() => EventBus.$emit(Events.navigation.navigated));

export default router;