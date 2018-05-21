import Routes from "../Routes";
import Missing from '@/components/Home/Missing'

export default [
  {
    path: Routes.Maintenance.System.Updates.route,
    name: Routes.Maintenance.System.Updates.name,
    component: Missing
  },
  {
    path: Routes.Maintenance.System.Environment.route,
    name: Routes.Maintenance.System.Environment.name,
    component: Missing
  },
  {
    path: Routes.Maintenance.System.Cache.route,
    name: Routes.Maintenance.System.Cache.name,
    component: Missing
  },
  {
    path: Routes.Maintenance.System.Version.route,
    name: Routes.Maintenance.System.Version.name,
    component: Missing
  },
  {
    path: Routes.Maintenance.System.PHP.route,
    name: Routes.Maintenance.System.PHP.name,
    component: Missing
  }
];