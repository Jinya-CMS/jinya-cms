import StartPage from '@/components/Home/StartPage';
import Routes from '@/router/Routes';
import Background from '@/img/start-background.png';

export default [
    {
        path: Routes.Home.StartPage.route,
            name: Routes.Home.StartPage.name,
            component: StartPage,
            meta: {
                title: 'routes.home.startpage',
                background: Background,
        },
    },
    ];
