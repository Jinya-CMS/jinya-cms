import Routes from '@/router/Routes';
import YoutubeVideoOverview from '@/components/Art/Video/SavedOnYoutube/Overview';
import YoutubeVideoAdd from '@/components/Art/Video/SavedOnYoutube/Add';
import YoutubeVideoEdit from '@/components/Art/Video/SavedOnYoutube/Edit';
import YoutubeVideoDetails from '@/components/Art/Video/SavedOnYoutube/Details';
import VideoOverview from '@/components/Art/Video/SavedInJinya/Overview';
import VideoAdd from '@/components/Art/Video/SavedInJinya/Add';
import VideoEdit from '@/components/Art/Video/SavedInJinya/Edit';
import VideoDetails from '@/components/Art/Video/SavedInJinya/Details';
import VideoUploader from '@/components/Art/Video/SavedInJinya/Uploader';
import Missing from '@/components/Home/Missing';

export default [
  {
    path: Routes.Art.Videos.SavedInJinya.Overview.route,
    name: Routes.Art.Videos.SavedInJinya.Overview.name,
    component: VideoOverview,
    meta: {
      title: 'routes.art.videos.saved_in_jinya.overview',
      searchEnabled: true,
    },
  },
  {
    path: Routes.Art.Videos.SavedInJinya.Add.route,
    name: Routes.Art.Videos.SavedInJinya.Add.name,
    component: VideoAdd,
    meta: {
      title: 'routes.art.videos.saved_in_jinya.add',
    },
  },
  {
    path: Routes.Art.Videos.SavedInJinya.Details.route,
    name: Routes.Art.Videos.SavedInJinya.Details.name,
    component: VideoDetails,
  },
  {
    path: Routes.Art.Videos.SavedInJinya.Edit.route,
    name: Routes.Art.Videos.SavedInJinya.Edit.name,
    component: VideoEdit,
  },
  {
    path: Routes.Art.Videos.SavedInJinya.Uploader.route,
    name: Routes.Art.Videos.SavedInJinya.Uploader.name,
    component: VideoUploader,
  },
  {
    path: Routes.Art.Videos.SavedOnYoutube.Overview.route,
    name: Routes.Art.Videos.SavedOnYoutube.Overview.name,
    component: YoutubeVideoOverview,
    meta: {
      title: 'routes.art.videos.saved_on_youtube.overview',
      searchEnabled: true,
    },
  },
  {
    path: Routes.Art.Videos.SavedOnYoutube.Add.route,
    name: Routes.Art.Videos.SavedOnYoutube.Add.name,
    component: YoutubeVideoAdd,
    meta: {
      title: 'routes.art.videos.saved_on_youtube.add',
    },
  },
  {
    path: Routes.Art.Videos.SavedOnYoutube.Details.route,
    name: Routes.Art.Videos.SavedOnYoutube.Details.name,
    component: YoutubeVideoDetails,
  },
  {
    path: Routes.Art.Videos.SavedOnYoutube.Edit.route,
    name: Routes.Art.Videos.SavedOnYoutube.Edit.name,
    component: YoutubeVideoEdit,
  },
  {
    path: Routes.Art.Videos.SavedOnVimeo.Overview.route,
    name: Routes.Art.Videos.SavedOnVimeo.Overview.name,
    component: Missing,
  },
  {
    path: Routes.Art.Videos.SavedOnVimeo.Add.route,
    name: Routes.Art.Videos.SavedOnVimeo.Add.name,
    component: Missing,
  },
  {
    path: Routes.Art.Videos.SavedOnVimeo.Details.route,
    name: Routes.Art.Videos.SavedOnVimeo.Details.name,
    component: Missing,
  },
  {
    path: Routes.Art.Videos.SavedOnVimeo.Edit.route,
    name: Routes.Art.Videos.SavedOnVimeo.Edit.name,
    component: Missing,
  },
  {
    path: Routes.Art.Videos.SavedOnNewgrounds.Overview.route,
    name: Routes.Art.Videos.SavedOnNewgrounds.Overview.name,
    component: Missing,
  },
  {
    path: Routes.Art.Videos.SavedOnNewgrounds.Add.route,
    name: Routes.Art.Videos.SavedOnNewgrounds.Add.name,
    component: Missing,
  },
  {
    path: Routes.Art.Videos.SavedOnNewgrounds.Details.route,
    name: Routes.Art.Videos.SavedOnNewgrounds.Details.name,
    component: Missing,
  },
  {
    path: Routes.Art.Videos.SavedOnNewgrounds.Edit.route,
    name: Routes.Art.Videos.SavedOnNewgrounds.Edit.name,
    component: Missing,
  },
];
