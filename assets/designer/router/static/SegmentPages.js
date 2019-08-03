import OverviewSegment from '@/components/Static/Pages/Segment/Overview';
import AddSegment from '@/components/Static/Pages/Segment/Add';
import EditSegment from '@/components/Static/Pages/Segment/Edit';
import DetailsSegment from '@/components/Static/Pages/Segment/Details';
import EditorSegment from '@/components/Static/Pages/Segment/Editor';
import Routes from '@/router/Routes';

export default [
    {
        path: Routes.Static.Pages.Segment.Overview.route,
        name: Routes.Static.Pages.Segment.Overview.name,
        component: OverviewSegment,
        meta: {
            searchEnabled: true,
            title: 'routes.static.pages.segment.overview',
        },
    },
    {
        path: Routes.Static.Pages.Segment.Add.route,
        name: Routes.Static.Pages.Segment.Add.name,
        component: AddSegment,
        meta: {
            title: 'routes.static.pages.segment.add',
        },
    },
    {
        path: Routes.Static.Pages.Segment.Details.route,
        name: Routes.Static.Pages.Segment.Details.name,
        component: DetailsSegment,
    },
    {
        path: Routes.Static.Pages.Segment.Edit.route,
        name: Routes.Static.Pages.Segment.Edit.name,
        component: EditSegment,
    },
    {
        path: Routes.Static.Pages.Segment.Editor.route,
        name: Routes.Static.Pages.Segment.Editor.name,
        component: EditorSegment,
    },
];
