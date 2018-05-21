import Routes from "../Routes";
import Missing from '@/components/Home/Missing'
import Password from '@/components/MyJinya/Account/Password'

export default [
  {
    path: Routes.MyJinya.Account.Profile.route,
    name: Routes.MyJinya.Account.Profile.name,
    component: Missing
  },
  {
    path: Routes.MyJinya.Account.Edit.route,
    name: Routes.MyJinya.Account.Edit.name,
    component: Missing
  },
  {
    path: Routes.MyJinya.Account.Password.route,
    name: Routes.MyJinya.Account.Password.name,
    component: Password,
    meta: {
      title: 'routes.my_jinya.account.password'
    }
  }
];