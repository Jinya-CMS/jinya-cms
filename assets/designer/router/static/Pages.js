import Missing from '@/components/Home/Missing';
import OverviewSavedInJinya from '@/components/Static/Pages/SavedInJinya/Overview';
import AddSavedInJinya from '@/components/Static/Pages/SavedInJinya/Add';
import EditSavedInJinya from '@/components/Static/Pages/SavedInJinya/Edit';
import DetailsSavedInJinya from '@/components/Static/Pages/SavedInJinya/Details';
import Routes from '@/router/Routes';

export default [
  {
    path: Routes.Static.Pages.SavedInJinya.Overview.route,
    name: Routes.Static.Pages.SavedInJinya.Overview.name,
    component: OverviewSavedInJinya,
    meta: {
      searchEnabled: true,
      title: 'routes.static.pages.saved_in_jinya.overview',
    },
  },
  {
    path: Routes.Static.Pages.SavedInJinya.Add.route,
    name: Routes.Static.Pages.SavedInJinya.Add.name,
    component: AddSavedInJinya,
    meta: {
      title: 'routes.static.pages.saved_in_jinya.add',
    },
  },
  {
    path: Routes.Static.Pages.SavedInJinya.Details.route,
    name: Routes.Static.Pages.SavedInJinya.Details.name,
    component: DetailsSavedInJinya,
  },
  {
    path: Routes.Static.Pages.SavedInJinya.Edit.route,
    name: Routes.Static.Pages.SavedInJinya.Edit.name,
    component: EditSavedInJinya,
  },
  {
    path: Routes.Static.Pages.SavedExternal.Overview.route,
    name: Routes.Static.Pages.SavedExternal.Overview.name,
    component: Missing,
  },
  {
    path: Routes.Static.Pages.SavedExternal.Add.route,
    name: Routes.Static.Pages.SavedExternal.Add.name,
    component: Missing,
  },
  {
    path: Routes.Static.Pages.SavedExternal.Details.route,
    name: Routes.Static.Pages.SavedExternal.Details.name,
    component: Missing,
  },
  {
    path: Routes.Static.Pages.SavedExternal.Edit.route,
    name: Routes.Static.Pages.SavedExternal.Edit.name,
    component: Missing,
  },
];
