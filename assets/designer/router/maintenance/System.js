import Update from '@/components/Maintenance/System/Update';
import Routes from '@/router/Routes';
import Version from '@/components/Maintenance/System/Version';
import PhpInfo from '@/components/Maintenance/System/PhpInfo';
import Cache from '@/components/Maintenance/System/Cache';
import Environment from '@/components/Maintenance/System/Environment';

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
    component: Environment,
    meta: {
      title: 'routes.maintenance.system.environment',
    },
  },
  {
    path: Routes.Maintenance.System.Cache.route,
    name: Routes.Maintenance.System.Cache.name,
    component: Cache,
    meta: {
      title: 'routes.maintenance.system.cache',
    },
  },
  {
    path: Routes.Maintenance.System.Version.route,
    name: Routes.Maintenance.System.Version.name,
    component: Version,
    meta: {
      title: 'routes.maintenance.system.version',
    },
  },
  {
    path: Routes.Maintenance.System.PHP.route,
    name: Routes.Maintenance.System.PHP.name,
    component: PhpInfo,
    meta: {
      title: 'routes.maintenance.system.phpinfo',
    },
  },
];
