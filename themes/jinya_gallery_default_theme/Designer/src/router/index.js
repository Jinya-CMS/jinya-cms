import Vue from 'vue';
import Router from 'vue-router';
import Routes from '@/router/Routes';
import EventBus from '@/framework/Events/EventBus';

import Account from './account';
import Art from '@/router/art';
import Static from '@/router/static';
import Home from '@/router/home';
import Configuration from '@/router/configuration';
import Maintenance from '@/router/maintenance';
import MyJinya from '@/router/myjinya';
import Error from '@/router/error';
import Events from '@/framework/Events/Events';
import Translator from '@/framework/i18n/Translator';
import DOMUtils from '@/framework/Utils/DOMUtils';
import { clearAuth, getApiKey, getCurrentUserRoles } from '@/framework/Storage/AuthStorage';

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
  routes,
});

router.beforeEach(async (to, from, next) => {
  const apiKey = getApiKey();

  if (!apiKey && to.name !== Routes.Account.Login.name) {
    next(Routes.Account.Login.route);
  } else {
    try {
      to.meta.me = {
        roles: getCurrentUserRoles(),
      };

      if (to.meta.role && !to.meta.me.roles.includes(to.meta.role)) {
        next(Routes.Error.NotAllowed.route);
      } else {
        EventBus.$emit(Events.navigation.navigating);
        DOMUtils.changeTitle(to.meta && to.meta.title ? Translator.message(to.meta.title) : '');
        next();
      }
    } catch (e) {
      clearAuth();
      next(Routes.Account.Login.route);
    }
  }
});

router.afterEach(() => EventBus.$emit(Events.navigation.navigated));

export default router;
