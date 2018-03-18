import Routes from "../Routes";
import StartPage from '@/components/Home/StartPage'

export default [
  {
    path: Routes.Home.StartPage.route,
    name: Routes.Home.StartPage.name,
    component: StartPage,
    meta: {
      title: 'routes.home.startpage',
      background: window.options.startBackground
    }
  }
];
