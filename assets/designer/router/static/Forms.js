import Missing from '@/components/Home/Missing';
import FormsOverview from '@/components/Static/Forms/Forms/Overview';
import FormsAdd from '@/components/Static/Forms/Forms/Add';
import FormsEdit from '@/components/Static/Forms/Forms/Edit';
import FormsBuilder from '@/components/Static/Forms/Forms/Builder';
import Routes from '@/router/Routes';

export default [
    {
        path: Routes.Static.Forms.Forms.Overview.route,
        name: Routes.Static.Forms.Forms.Overview.name,
        component: FormsOverview,
        meta: {
            title: 'routes.static.forms.forms.overview',
        },
    },
    {
        path: Routes.Static.Forms.Forms.Add.route,
        name: Routes.Static.Forms.Forms.Add.name,
        component: FormsAdd,
        meta: {
            title: 'routes.static.forms.forms.add',
        },
    },
    {
        path: Routes.Static.Forms.Forms.Items.route,
        name: Routes.Static.Forms.Forms.Items.name,
        component: FormsBuilder,
    },
    {
        path: Routes.Static.Forms.Forms.Edit.route,
        name: Routes.Static.Forms.Forms.Edit.name,
        component: FormsEdit,
    },
    {
        path: Routes.Static.Forms.EmailTemplates.Overview.route,
        name: Routes.Static.Forms.EmailTemplates.Overview.name,
        component: Missing,
    },
    {
        path: Routes.Static.Forms.EmailTemplates.Add.route,
        name: Routes.Static.Forms.EmailTemplates.Add.name,
        component: Missing,
    },
    {
        path: Routes.Static.Forms.EmailTemplates.Details.route,
        name: Routes.Static.Forms.EmailTemplates.Details.name,
        component: Missing,
    },
    {
        path: Routes.Static.Forms.EmailTemplates.Edit.route,
        name: Routes.Static.Forms.EmailTemplates.Edit.name,
        component: Missing,
    },
    {
        path: Routes.Static.Forms.Requests.Overview.route,
        name: Routes.Static.Forms.Requests.Overview.name,
        component: Missing,
    },
    {
        path: Routes.Static.Forms.Requests.Reply.route,
        name: Routes.Static.Forms.Requests.Reply.name,
        component: Missing,
    },
    {
        path: Routes.Static.Forms.Requests.Details.route,
        name: Routes.Static.Forms.Requests.Details.name,
        component: Missing,
    },
];
