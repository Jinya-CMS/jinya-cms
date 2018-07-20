import Routes from '@/router/Routes';
import NotAllowed from '@/components/Error/NotAllowed';

export default [
  {
    path: Routes.Error.NotAllowed.route,
    name: Routes.Error.NotAllowed.name,
    component: NotAllowed,
    meta: {
      title: 'routes.error.not_allowed',
    },
  },
];
