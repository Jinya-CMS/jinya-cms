import Routes from "../Routes";
import Missing from "@/components/Home/Missing";
import ArtistsOverview from '@/components/Configuration/Artists/Overview';
import AddArtist from '@/components/Configuration/Artists/Add';
import EditArtist from '@/components/Configuration/Artists/Edit';
import ArtistChangePassword from '@/components/Configuration/Artists/ChangePassword';

export default [
  {
    path: Routes.Configuration.General.Artists.Overview.route,
    name: Routes.Configuration.General.Artists.Overview.name,
    component: ArtistsOverview,
    meta: {
      title: 'routes.configuration.general.artists.overview'
    }
  },
  {
    path: Routes.Configuration.General.Artists.Add.route,
    name: Routes.Configuration.General.Artists.Add.name,
    component: AddArtist,
    meta: {
      title: 'routes.configuration.general.artists.add'
    }
  },
  {
    path: Routes.Configuration.General.Artists.Details.route,
    name: Routes.Configuration.General.Artists.Details.name,
    component: Missing
  },
  {
    path: Routes.Configuration.General.Artists.Edit.route,
    name: Routes.Configuration.General.Artists.Edit.name,
    component: EditArtist
  },
  {
    path: Routes.Configuration.General.Artists.ChangePassword.route,
    name: Routes.Configuration.General.Artists.ChangePassword.name,
    component: ArtistChangePassword
  }
]