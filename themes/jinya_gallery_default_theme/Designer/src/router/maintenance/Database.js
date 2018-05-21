import Routes from "../Routes";
import Missing from '@/components/Home/Missing'

export default [
  {
    path: Routes.Maintenance.Database.MySQL.route,
    name: Routes.Maintenance.Database.MySQL.name,
    component: Missing
  },
  {
    path: Routes.Maintenance.Database.Tool.route,
    name: Routes.Maintenance.Database.Tool.name,
    component: Missing
  }
];