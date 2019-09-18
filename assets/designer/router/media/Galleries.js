import Routes from '@/router/Routes';
import Overview from '@/components/Media/Gallery/Overview';
import Arrange from '@/components/Media/Gallery/Arrange';

export default [
    {
        path: Routes.Media.Galleries.Overview.route,
            name: Routes.Media.Galleries.Overview.name,
            component: Overview,
            meta: {
                title: 'routes.media.galleries.overview',
                searchEnabled: true,
        },
    },
    {
        path: Routes.Media.Galleries.Arrange.route,
        name: Routes.Media.Galleries.Arrange.name,
        component: Arrange,
        meta: {
            title: 'routes.media.galleries.arrange',
        },
    },
    ];
