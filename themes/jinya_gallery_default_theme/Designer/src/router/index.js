import Vue from 'vue'
import Router from 'vue-router'
import StartPage from '@/components/Home/StartPage'
import Login from '@/components/Account/Login'
import Lockr from 'lockr';

Vue.use(Router);

const router = new Router({
  mode: 'history',
  routes: [
    {
      path: '/designer',
      name: 'DesignerRoot',
      component: StartPage
    },
    {
      path: '/designer/login',
      name: 'Login',
      component: Login
    }
  ]
});

router.beforeEach((to, from, next) => {
  if (!Lockr.get('JinyaApiKey') && to.name !== 'Login') {
    next({name: 'Login'});
  } else {
    next();
  }
});

export default router;