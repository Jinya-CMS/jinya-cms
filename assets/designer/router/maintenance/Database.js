import Routes from '@/router/Routes';
import Tool from '@/components/Maintenance/Database/Tool';
import Info from '@/components/Maintenance/Database/Info';

export default [
  {
    path: Routes.Maintenance.Database.Info.route,
    name: Routes.Maintenance.Database.Info.name,
    component: Info,
    meta: {
      title: 'routes.maintenance.database.info',
    },
  },
  {
    path: Routes.Maintenance.Database.Tool.route,
    name: Routes.Maintenance.Database.Tool.name,
    component: Tool,
    meta: {
      title: 'routes.maintenance.database.tool',
    },
  },
];
