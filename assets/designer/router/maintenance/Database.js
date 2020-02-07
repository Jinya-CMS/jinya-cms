import Missing from '@/components/Home/Missing';
import Routes from '@/router/Routes';

export default [
  {
        path: Routes.Maintenance.Database.MySQL.route,
            name: Routes.Maintenance.Database.MySQL.name,
            component: Missing,
    },
    {
        path: Routes.Maintenance.Database.Tool.route,
        name: Routes.Maintenance.Database.Tool.name,
        component: Missing,
    },
    ];
