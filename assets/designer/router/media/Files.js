import FileBrowser from '@/components/Media/FileBrowser/FileBrowser';
import Routes from '@/router/Routes';

export default [
  {
    path: Routes.Media.Files.FileBrowser.route,
    name: Routes.Media.Files.FileBrowser.name,
    component: FileBrowser,
    meta: {
      title: 'routes.media.files',
      searchEnabled: true,
    },
  },
];
