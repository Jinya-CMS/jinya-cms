import Routes from "../Routes";
import Missing from '@/components/Home/Missing'
import ArtworksSavedInJinyaOverview from '@/components/Art/Artworks/SavedInJinya/Overview'
import ArtworksSavedInJinyaOverviewAdd from '@/components/Art/Artworks/SavedInJinya/AddNavbarItem'

export default [
  {
    path: Routes.Art.Artworks.SavedInJinya.Overview.route,
    name: Routes.Art.Artworks.SavedInJinya.Overview.name,
    component: ArtworksSavedInJinyaOverview,
    meta: {
      title: 'routes.art.artworks.saved_in_jinya.overview',
      navbar: {
        start: [
          ArtworksSavedInJinyaOverviewAdd
        ]
      }
    }
  },
  {
    path: Routes.Art.Artworks.SavedInJinya.Add.route,
    name: Routes.Art.Artworks.SavedInJinya.Add.name,
    component: Missing
  },
  {
    path: Routes.Art.Artworks.SavedInJinya.Details.route,
    name: Routes.Art.Artworks.SavedInJinya.Details.name,
    component: Missing
  },
  {
    path: Routes.Art.Artworks.SavedInJinya.Edit.route,
    name: Routes.Art.Artworks.SavedInJinya.Edit.name,
    component: Missing
  },
  {
    path: Routes.Art.Artworks.SavedExternal.Overview.route,
    name: Routes.Art.Artworks.SavedExternal.Overview.name,
    component: Missing
  },
  {
    path: Routes.Art.Artworks.SavedExternal.Add.route,
    name: Routes.Art.Artworks.SavedExternal.Add.name,
    component: Missing
  },
  {
    path: Routes.Art.Artworks.SavedExternal.Details.route,
    name: Routes.Art.Artworks.SavedExternal.Details.name,
    component: Missing
  },
  {
    path: Routes.Art.Artworks.SavedExternal.Edit.route,
    name: Routes.Art.Artworks.SavedExternal.Edit.name,
    component: Missing
  }
];