import Routes from "../Routes";
import Missing from '@/components/Home/Missing'
import GalleriesArtOverview from '@/components/Art/Galleries/Art/Overview'
import GalleriesArtAdd from '@/components/Art/Galleries/Art/Add'
import GalleriesArtEdit from '@/components/Art/Galleries/Art/Edit'
import GalleriesArtDetails from '@/components/Art/Galleries/Art/Details'
import GalleriesArtDesigner from '@/components/Art/Galleries/Art/Designer'

export default [
  {
    path: Routes.Art.Galleries.Art.Overview.route,
    name: Routes.Art.Galleries.Art.Overview.name,
    component: GalleriesArtOverview,
    meta: {
      title: 'routes.art.galleries.art.overview',
      searchEnabled: true
    }
  },
  {
    path: Routes.Art.Galleries.Art.Add.route,
    name: Routes.Art.Galleries.Art.Add.name,
    component: GalleriesArtAdd,
    meta: {
      title: 'routes.art.galleries.art.add'
    }
  },
  {
    path: Routes.Art.Galleries.Art.Details.route,
    name: Routes.Art.Galleries.Art.Details.name,
    component: GalleriesArtDetails
  },
  {
    path: Routes.Art.Galleries.Art.Designer.route,
    name: Routes.Art.Galleries.Art.Designer.name,
    component: GalleriesArtDesigner,
    meta: {
      title: 'routes.art.galleries.art.designer'
    }
  },
  {
    path: Routes.Art.Galleries.Art.Edit.route,
    name: Routes.Art.Galleries.Art.Edit.name,
    component: GalleriesArtEdit,
    meta: {
      title: 'routes.art.galleries.art.edit'
    }
  },
  {
    path: Routes.Art.Galleries.Video.Overview.route,
    name: Routes.Art.Galleries.Video.Overview.name,
    component: Missing
  },
  {
    path: Routes.Art.Galleries.Video.Add.route,
    name: Routes.Art.Galleries.Video.Add.name,
    component: Missing
  },
  {
    path: Routes.Art.Galleries.Video.Details.route,
    name: Routes.Art.Galleries.Video.Details.name,
    component: Missing
  },
  {
    path: Routes.Art.Galleries.Video.Edit.route,
    name: Routes.Art.Galleries.Video.Edit.name,
    component: Missing
  }
];