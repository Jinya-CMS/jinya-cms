import Routes from "../Routes";
import Missing from '@/components/Home/Missing'
import OverviewSavedInJinya from '@/components/Static/Pages/Overview'

export default [
  {
    path: Routes.Static.Pages.SavedInJinya.Overview.route,
    name: Routes.Static.Pages.SavedInJinya.Overview.name,
    component: OverviewSavedInJinya,
    meta: {
      searchEnabled: true,
      title: ''
    }
  },
  {
    path: Routes.Static.Pages.SavedInJinya.Add.route,
    name: Routes.Static.Pages.SavedInJinya.Add.name,
    component: Missing
  },
  {
    path: Routes.Static.Pages.SavedInJinya.Details.route,
    name: Routes.Static.Pages.SavedInJinya.Details.name,
    component: Missing
  },
  {
    path: Routes.Static.Pages.SavedInJinya.Edit.route,
    name: Routes.Static.Pages.SavedInJinya.Edit.name,
    component: Missing
  },
  {
    path: Routes.Static.Pages.SavedExternal.Overview.route,
    name: Routes.Static.Pages.SavedExternal.Overview.name,
    component: Missing
  },
  {
    path: Routes.Static.Pages.SavedExternal.Add.route,
    name: Routes.Static.Pages.SavedExternal.Add.name,
    component: Missing
  },
  {
    path: Routes.Static.Pages.SavedExternal.Details.route,
    name: Routes.Static.Pages.SavedExternal.Details.name,
    component: Missing
  },
  {
    path: Routes.Static.Pages.SavedExternal.Edit.route,
    name: Routes.Static.Pages.SavedExternal.Edit.name,
    component: Missing
  }
];