import Missing from '@/components/Home/Missing';
import Routes from '@/router/Routes';

export default [
    {
        path: Routes.Media.Galleries.Overview.route,
        name: Routes.Media.Galleries.Overview.name,
        component: Missing,
        meta: {
            title: 'routes.media.galleries.overview',
            searchEnabled: true,
        },
    },
    {
        path: Routes.Media.Galleries.Add.route,
        name: Routes.Media.Galleries.Add.name,
        component: Missing,
        meta: {
            title: 'routes.media.galleries.add',
        },
    },
    {
        path: Routes.Media.Galleries.Details.route,
        name: Routes.Media.Galleries.Details.name,
        component: Missing,
    },
    {
        path: Routes.Media.Galleries.Edit.route,
        name: Routes.Media.Galleries.Edit.name,
        component: Missing,
    },
];
