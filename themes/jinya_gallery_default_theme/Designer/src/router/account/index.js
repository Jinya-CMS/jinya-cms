import Login from '@/components/Account/Login';
import Routes from '@/router/Routes';

export default [
  {
    path: Routes.Account.Login.route,
    name: Routes.Account.Login.name,
    component: Login,
    meta: {
      title: 'routes.account.login',
      background: window.options.startBackground,
    },
  },
];
