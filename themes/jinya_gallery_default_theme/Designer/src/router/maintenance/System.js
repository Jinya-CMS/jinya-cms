import Missing from '@/components/Home/Missing';
import Update from '@/components/Maintenance/System/Update';
import Routes from '@/router/Routes';

export default [
  {
    path: Routes.Maintenance.System.Updates.route,
    name: Routes.Maintenance.System.Updates.name,
    component: Update,
    meta: {
      title: 'routes.maintenance.system.updates',
    },
  },
  {
    path: Routes.Maintenance.System.Environment.route,
    name: Routes.Maintenance.System.Environment.name,
    component: Missing,
  },
  {
    path: Routes.Maintenance.System.Cache.route,
    name: Routes.Maintenance.System.Cache.name,
    component: Missing,
  },
  {
    path: Routes.Maintenance.System.Version.route,
    name: Routes.Maintenance.System.Version.name,
    component: Missing,
  },
  {
    path: Routes.Maintenance.System.PHP.route,
    name: Routes.Maintenance.System.PHP.name,
    component: Missing,
  },
];
