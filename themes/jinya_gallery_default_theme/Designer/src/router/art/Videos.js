import Routes from "../Routes";
import YoutubeVideoOverview from '@/components/Art/Video/SavedOnYoutube/Overview';
import YoutubeVideoAdd from '@/components/Art/Video/SavedOnYoutube/Add';
import Missing from '@/components/Home/Missing'

export default [
  {
    path: Routes.Art.Videos.SavedInJinya.Overview.route,
    name: Routes.Art.Videos.SavedInJinya.Overview.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedInJinya.Add.route,
    name: Routes.Art.Videos.SavedInJinya.Add.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedInJinya.Details.route,
    name: Routes.Art.Videos.SavedInJinya.Details.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedInJinya.Edit.route,
    name: Routes.Art.Videos.SavedInJinya.Edit.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnYoutube.Overview.route,
    name: Routes.Art.Videos.SavedOnYoutube.Overview.name,
    component: YoutubeVideoOverview,
    meta: {
      title: 'routes.art.videos.saved_on_youtube.overview',
      searchEnabled: true
    }
  },
  {
    path: Routes.Art.Videos.SavedOnYoutube.Add.route,
    name: Routes.Art.Videos.SavedOnYoutube.Add.name,
    component: YoutubeVideoAdd,
    meta: {
      title: 'routes.art.videos.saved_on_youtube.add'
    }
  },
  {
    path: Routes.Art.Videos.SavedOnYoutube.Details.route,
    name: Routes.Art.Videos.SavedOnYoutube.Details.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnYoutube.Edit.route,
    name: Routes.Art.Videos.SavedOnYoutube.Edit.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnVimeo.Overview.route,
    name: Routes.Art.Videos.SavedOnVimeo.Overview.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnVimeo.Add.route,
    name: Routes.Art.Videos.SavedOnVimeo.Add.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnVimeo.Details.route,
    name: Routes.Art.Videos.SavedOnVimeo.Details.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnVimeo.Edit.route,
    name: Routes.Art.Videos.SavedOnVimeo.Edit.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnNewgrounds.Overview.route,
    name: Routes.Art.Videos.SavedOnNewgrounds.Overview.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnNewgrounds.Add.route,
    name: Routes.Art.Videos.SavedOnNewgrounds.Add.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnNewgrounds.Details.route,
    name: Routes.Art.Videos.SavedOnNewgrounds.Details.name,
    component: Missing
  },
  {
    path: Routes.Art.Videos.SavedOnNewgrounds.Edit.route,
    name: Routes.Art.Videos.SavedOnNewgrounds.Edit.name,
    component: Missing
  }
]