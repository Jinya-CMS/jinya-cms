import Missing from '@/components/Home/Missing';
import Password from '@/components/MyJinya/Account/Password';
import ApiKeys from '@/components/MyJinya/Account/ApiKeys';
import Routes from '@/router/Routes';
import MyJinya from '@/components/MyJinya/MyJinya';

export default [
  {
        path: Routes.MyJinya.Account.Profile.route,
            name: Routes.MyJinya.Account.Profile.name,
            component: MyJinya,
    },
    {
        path: Routes.MyJinya.Account.Edit.route,
        name: Routes.MyJinya.Account.Edit.name,
        component: Missing,
    },
    {
        path: Routes.MyJinya.Account.Password.route,
        name: Routes.MyJinya.Account.Password.name,
        component: Password,
        meta: {
            title: 'routes.my_jinya.account.password',
        },
    },
    {
        path: Routes.MyJinya.Account.ApiKeys.route,
        name: Routes.MyJinya.Account.ApiKeys.name,
        component: ApiKeys,
        meta: {
            title: 'routes.my_jinya.account.api_keys',
        },
    },
    ];
