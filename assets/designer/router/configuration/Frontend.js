import ThemeOverview from '@/components/Configuration/Frontend/Themes/Overview';
import ThemeVariables from '@/components/Configuration/Frontend/Themes/Variables';
import ThemeConfiguration from '@/components/Configuration/Frontend/Themes/Configuration';
import ThemeLinks from '@/components/Configuration/Frontend/Themes/Links';
import MenuOverview from '@/components/Configuration/Frontend/Menus/Overview';
import MenuAdd from '@/components/Configuration/Frontend/Menus/Add';
import MenuEdit from '@/components/Configuration/Frontend/Menus/Edit';
import MenuBuilder from '@/components/Configuration/Frontend/Menus/Builder';
import Routes from '@/router/Routes';

export default [
  {
        path: Routes.Configuration.Frontend.Menu.Overview.route,
            name: Routes.Configuration.Frontend.Menu.Overview.name,
            component: MenuOverview,
            meta: {
                searchEnabled: true,
                title: 'routes.configuration.frontend.menus.overview',
        },
    },
    {
        path: Routes.Configuration.Frontend.Menu.Builder.route,
        name: Routes.Configuration.Frontend.Menu.Builder.name,
        component: MenuBuilder,
    },
    {
        path: Routes.Configuration.Frontend.Menu.Edit.route,
        name: Routes.Configuration.Frontend.Menu.Edit.name,
        component: MenuEdit,
    },
    {
        path: Routes.Configuration.Frontend.Menu.Add.route,
        name: Routes.Configuration.Frontend.Menu.Add.name,
        component: MenuAdd,
        meta: {
            title: 'routes.configuration.frontend.menus.add',
        },
    },
    {
        path: Routes.Configuration.Frontend.Theme.Overview.route,
        name: Routes.Configuration.Frontend.Theme.Overview.name,
        component: ThemeOverview,
        meta: {
            title: 'routes.configuration.frontend.themes.overview',
        },
    },
    {
        path: Routes.Configuration.Frontend.Theme.Settings.route,
        name: Routes.Configuration.Frontend.Theme.Settings.name,
        component: ThemeConfiguration,
    },
    {
        path: Routes.Configuration.Frontend.Theme.Links.route,
        name: Routes.Configuration.Frontend.Theme.Links.name,
        component: ThemeLinks,
    },
    {
        path: Routes.Configuration.Frontend.Theme.Variables.route,
        name: Routes.Configuration.Frontend.Theme.Variables.name,
        component: ThemeVariables,
        meta: {
            title: 'routes.configuration.frontend.themes.variables',
            searchEnabled: true,
        },
    },
    ];
