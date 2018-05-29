import Routes from "../Routes";
import Missing from '@/components/Home/Missing'
import ArtworksSavedInJinyaOverview from '@/components/Art/Artworks/SavedInJinya/Overview'
import ArtworksSavedInJinyaAdd from '@/components/Art/Artworks/SavedInJinya/Add'
import ArtworksSavedInJinyaEdit from '@/components/Art/Artworks/SavedInJinya/Edit'
import ArtworksSavedInJinyaDetails from '@/components/Art/Artworks/SavedInJinya/Details'

export default [
  {
    path: Routes.Art.Artworks.SavedInJinya.Overview.route,
    name: Routes.Art.Artworks.SavedInJinya.Overview.name,
    component: ArtworksSavedInJinyaOverview,
    meta: {
      title: 'routes.art.artworks.saved_in_jinya.overview',
      searchEnabled: true,
      role: 'ROLE_WRITER'
    }
  },
  {
    path: Routes.Art.Artworks.SavedInJinya.Add.route,
    name: Routes.Art.Artworks.SavedInJinya.Add.name,
    component: ArtworksSavedInJinyaAdd,
    meta: {
      title: 'routes.art.artworks.saved_in_jinya.add',
      role: 'ROLE_WRITER'
    }
  },
  {
    path: Routes.Art.Artworks.SavedInJinya.Details.route,
    name: Routes.Art.Artworks.SavedInJinya.Details.name,
    component: ArtworksSavedInJinyaDetails,
    meta: {
      role: 'ROLE_WRITER'
    }
  },
  {
    path: Routes.Art.Artworks.SavedInJinya.Edit.route,
    name: Routes.Art.Artworks.SavedInJinya.Edit.name,
    component: ArtworksSavedInJinyaEdit,
    meta: {
      title: 'routes.art.artworks.saved_in_jinya.edit',
      role: 'ROLE_WRITER'
    }
  },
  {
    path: Routes.Art.Artworks.SavedExternal.Overview.route,
    name: Routes.Art.Artworks.SavedExternal.Overview.name,
    component: Missing,
    meta: {
      role: 'ROLE_WRITER'
    }
  },
  {
    path: Routes.Art.Artworks.SavedExternal.Add.route,
    name: Routes.Art.Artworks.SavedExternal.Add.name,
    component: Missing,
    meta: {
      role: 'ROLE_WRITER'
    }
  },
  {
    path: Routes.Art.Artworks.SavedExternal.Details.route,
    name: Routes.Art.Artworks.SavedExternal.Details.name,
    component: Missing,
    meta: {
      role: 'ROLE_WRITER'
    }
  },
  {
    path: Routes.Art.Artworks.SavedExternal.Edit.route,
    name: Routes.Art.Artworks.SavedExternal.Edit.name,
    component: Missing,
    meta: {
      role: 'ROLE_WRITER'
    }
  }
];