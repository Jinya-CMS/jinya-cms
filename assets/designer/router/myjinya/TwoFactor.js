import Routes from '@/router/Routes';
import KnownDevices from '@/components/MyJinya/TwoFactor/KnownDevices';

export default [
  {
        path: Routes.MyJinya.TwoFactor.KnownDevices.route,
            name: Routes.MyJinya.TwoFactor.KnownDevices.name,
            component: KnownDevices,
            meta:
            {
                title: 'routes.my_jinya.two_factor.known_devices',
        },
    },
    ];
