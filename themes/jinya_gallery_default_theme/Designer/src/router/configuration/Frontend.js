import Routes from "../Routes";
import Missing from "@/components/Home/Missing";
import ThemeOverview from "@/components/Configuration/Frontend/Themes/Overview";
import ThemeVariables from "@/components/Configuration/Frontend/Themes/Variables";
import ThemeConfiguration from "@/components/Configuration/Frontend/Themes/Configuration";
import MenuOverview from '@/components/Configuration/Frontend/Menus/Overview';

export default [
  {
    path: Routes.Configuration.Frontend.Menu.Overview.route,
    name: Routes.Configuration.Frontend.Menu.Overview.name,
    component: MenuOverview,
    meta: {
      searchEnabled: true,
      title: 'routes.configuration.frontend.menus.overview'
    }
  },
  {
    path: Routes.Configuration.Frontend.Menu.Add.route,
    name: Routes.Configuration.Frontend.Menu.Add.name,
    component: Missing
  },
  {
    path: Routes.Configuration.Frontend.Menu.Editor.route,
    name: Routes.Configuration.Frontend.Menu.Editor.name,
    component: Missing
  },
  {
    path: Routes.Configuration.Frontend.Menu.Edit.route,
    name: Routes.Configuration.Frontend.Menu.Edit.name,
    component: Missing
  },
  {
    path: Routes.Configuration.Frontend.Theme.Overview.route,
    name: Routes.Configuration.Frontend.Theme.Overview.name,
    component: ThemeOverview,
    meta: {
      title: 'routes.configuration.frontend.themes.overview'
    }
  },
  {
    path: Routes.Configuration.Frontend.Theme.Settings.route,
    name: Routes.Configuration.Frontend.Theme.Settings.name,
    component: ThemeConfiguration
  },
  {
    path: Routes.Configuration.Frontend.Theme.Variables.route,
    name: Routes.Configuration.Frontend.Theme.Variables.name,
    component: ThemeVariables,
    meta: {
      title: 'routes.configuration.frontend.themes.variables',
      searchEnabled: true
    }
  }
]