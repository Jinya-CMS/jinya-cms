import Routes from "../Routes";
import Login from '@/components/Account/Login'

export default [
  {
    path: Routes.Account.Login.route,
    name: Routes.Account.Login.name,
    component: Login
  }
];