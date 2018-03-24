<template>
    <header class="jinya-menu">
        <jinya-menu-navbar @hamburger-mouseover="isHover = true" @hamburger-mouseout="isHover = false"
                           @hamburger-click="isOpen = !isOpen">
            <span class="jinya-menu__header" v-if="$route.meta.title">{{$route.meta.title|jmessage}}</span>
            <jinya-menu-navbar-search-item v-if="$route.meta.searchEnabled"/>
        </jinya-menu-navbar>
        <jinya-menu-flyout @mouseover.native="isHover = true" @mouseout.native="isHover = false">
            <jinya-menu-flyout-navbar slot="flyout-navbar" :is-open="isOpen || isHover">
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.art.navbar" to="Login"
                                               :is-selected="selectedHeader === 'art'"
                                               @selected="selectHeader('art')"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.static.navbar" to="Login"
                                               :is-selected="selectedHeader === 'static'"
                                               @selected="selectHeader('static')"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.configuration.navbar" to="Login"
                                               :is-selected="selectedHeader === 'config'"
                                               @selected="selectHeader('config')"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.maintenance.navbar" to="Login"
                                               :is-selected="selectedHeader === 'maintenance'"
                                               @selected="selectHeader('maintenance')"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.my_jinya.navbar" to="Login"
                                               :is-selected="selectedHeader === 'my-jinya'"
                                               @selected="selectHeader('my-jinya')"/>
            </jinya-menu-flyout-navbar>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'art'">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.art.sections.artworks.header">
                    <jinya-menu-flyout-menu-item to="Art.Artworks.SavedInJinya.Overview"
                                                 text="menu.designer.flyout.art.sections.artworks.saved_in_jinya"/>
                    <jinya-menu-flyout-menu-item to="Art.Artworks.SavedExternal.Overview"
                                                 text="menu.designer.flyout.art.sections.artworks.saved_external"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.art.sections.videos.header">
                    <jinya-menu-flyout-menu-item to="Art.Videos.SavedInJinya.Overview"
                                                 text="menu.designer.flyout.art.sections.videos.saved_in_jinya"/>
                    <jinya-menu-flyout-menu-item to="Art.Videos.SavedOnYoutube.Overview"
                                                 text="menu.designer.flyout.art.sections.videos.saved_on_youtube"/>
                    <jinya-menu-flyout-menu-item to="Art.Videos.SavedOnVimeo.Overview"
                                                 text="menu.designer.flyout.art.sections.videos.saved_on_vimeo"/>
                    <jinya-menu-flyout-menu-item to="Art.Videos.SavedOnNewgrounds.Overview"
                                                 text="menu.designer.flyout.art.sections.videos.saved_on_newgrounds"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.art.sections.galleries.header">
                    <jinya-menu-flyout-menu-item to="Art.Galleries.Art.Overview"
                                                 text="menu.designer.flyout.art.sections.galleries.artwork_galleries"/>
                    <jinya-menu-flyout-menu-item to="Art.Galleries.Video.Overview"
                                                 text="menu.designer.flyout.art.sections.galleries.video_galleries"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'static'">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.static.sections.pages.header">
                    <jinya-menu-flyout-menu-item to="Static.Pages.SavedInJinya.Overview"
                                                 text="menu.designer.flyout.static.sections.pages.saved_in_jinya"/>
                    <jinya-menu-flyout-menu-item to="Static.Pages.SavedExternal.Overview"
                                                 text="menu.designer.flyout.static.sections.pages.saved_external"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.static.sections.forms.header">
                    <jinya-menu-flyout-menu-item to="Static.Forms.Forms.Overview"
                                                 text="menu.designer.flyout.static.sections.forms.forms"/>
                    <jinya-menu-flyout-menu-item to="Static.Forms.Requests.Overview"
                                                 text="menu.designer.flyout.static.sections.forms.requests"/>
                    <jinya-menu-flyout-menu-item to="Static.Forms.EmailTemplates.Overview"
                                                 text="menu.designer.flyout.static.sections.forms.email_templates"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'config'">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.configuration.sections.general.header">
                    <jinya-menu-flyout-menu-item to="Configuration.General.Artists.Overview"
                                                 text="menu.designer.flyout.configuration.sections.general.artists"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.configuration.sections.frontend.header">
                    <jinya-menu-flyout-menu-item to="Configuration.Frontend.Theme.Overview"
                                                 text="menu.designer.flyout.configuration.sections.frontend.themes"/>
                    <jinya-menu-flyout-menu-item to="Configuration.Frontend.Menu.Overview"
                                                 text="menu.designer.flyout.configuration.sections.frontend.menus"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'maintenance'">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.system.header">
                    <jinya-menu-flyout-menu-item to="Maintenance.System.Updates"
                                                 text="menu.designer.flyout.maintenance.sections.system.updates"/>
                    <jinya-menu-flyout-menu-item to="Maintenance.System.Environment"
                                                 text="menu.designer.flyout.maintenance.sections.system.environment"/>
                    <jinya-menu-flyout-menu-item to="Maintenance.System.Cache"
                                                 text="menu.designer.flyout.maintenance.sections.system.cache"/>
                    <jinya-menu-flyout-menu-item to="Maintenance.System.Version"
                                                 text="menu.designer.flyout.maintenance.sections.system.version"/>
                    <jinya-menu-flyout-menu-item to="Maintenance.System.PHP"
                                                 text="menu.designer.flyout.maintenance.sections.system.php"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.database.header">
                    <jinya-menu-flyout-menu-item to="Maintenance.Database.MySQL"
                                                 text="menu.designer.flyout.maintenance.sections.database.mysql_information"/>
                    <jinya-menu-flyout-menu-item to="Maintenance.Database.Tool"
                                                 text="menu.designer.flyout.maintenance.sections.database.database_tool"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.diagnosis.header">
                    <jinya-menu-flyout-menu-item to="Maintenance.Diagnosis.ApplicationLog.Overview"
                                                 text="menu.designer.flyout.maintenance.sections.diagnosis.application_log"/>
                    <jinya-menu-flyout-menu-item to="Maintenance.Diagnosis.AccessLog.Overview"
                                                 text="menu.designer.flyout.maintenance.sections.diagnosis.access_log"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'my-jinya'">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.my_jinya.sections.account.header">
                    <jinya-menu-flyout-menu-item to="MyJinya.Account.Profile"
                                                 text="menu.designer.flyout.my_jinya.sections.account.profile"/>
                    <jinya-menu-flyout-menu-item to="MyJinya.Account.Password"
                                                 text="menu.designer.flyout.my_jinya.sections.account.password"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.my_jinya.sections.created_by_me.header">
                    <jinya-menu-flyout-menu-item to="MyJinya.CreatedByMe.Artworks"
                                                 text="menu.designer.flyout.my_jinya.sections.created_by_me.artworks"/>
                    <jinya-menu-flyout-menu-item to="MyJinya.CreatedByMe.Videos"
                                                 text="menu.designer.flyout.my_jinya.sections.created_by_me.videos"/>
                    <jinya-menu-flyout-menu-item to="MyJinya.CreatedByMe.Galleries"
                                                 text="menu.designer.flyout.my_jinya.sections.created_by_me.galleries"/>
                    <jinya-menu-flyout-menu-item to="MyJinya.CreatedByMe.Pages"
                                                 text="menu.designer.flyout.my_jinya.sections.created_by_me.pages"/>
                    <jinya-menu-flyout-menu-item to="MyJinya.CreatedByMe.Fpr,s"
                                                 text="menu.designer.flyout.my_jinya.sections.created_by_me.forms"/>
                    <jinya-menu-flyout-menu-item to="MyJinya.CreatedByMe.Menus"
                                                 text="menu.designer.flyout.my_jinya.sections.created_by_me.menus"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
        </jinya-menu-flyout>
    </header>
</template>

<script>
  import JinyaMenuNavbar from "@/components/Framework/Markup/Menu/Navbar/JinyaMenuNavbar";
  import JinyaMenuNavbarItem from "@/components/Framework/Markup/Menu/Navbar/JinyaMenuNavbarItem";
  import JinyaMenuFlyout from "@/components/Framework/Markup/Menu/Flyout/JinyaMenuFlyout";
  import JinyaMenuFlyoutNavbarItem from "@/components/Framework/Markup/Menu/Flyout/JinyaMenuFlyoutNavbarItem";
  import JinyaMenuFlyoutNavbar from "@/components/Framework/Markup/Menu/Flyout/JinyaMenuFlyoutNavbar";
  import JinyaMenuFlyoutMenu from "@/components/Framework/Markup/Menu/Flyout/JinyaMenuFlyoutMenu";
  import JinyaMenuFlyoutMenuSection from "@/components/Framework/Markup/Menu/Flyout/JinyaMenuFlyoutMenuSection";
  import JinyaMenuFlyoutMenuItem from "@/components/Framework/Markup/Menu/Flyout/JinyaMenuFlyoutMenuItem";
  import JinyaMenuNavbarSearchItem from "@/components/Framework/Markup/Menu/Navbar/JinyaMenuNavbarSearchItem";
  import EventBus from "../../Events/EventBus";
  import Events from "../../Events/Events";

  // noinspection JSUnusedGlobalSymbols
  export default {
    components: {
      JinyaMenuNavbarSearchItem,
      JinyaMenuFlyoutMenuItem,
      JinyaMenuFlyoutMenuSection,
      JinyaMenuFlyoutMenu,
      JinyaMenuFlyoutNavbar,
      JinyaMenuFlyoutNavbarItem,
      JinyaMenuFlyout,
      JinyaMenuNavbarItem,
      JinyaMenuNavbar
    },
    methods: {
      selectHeader(name) {
        this.selectedHeader = name;
        this.isOpen = true;
      }
    },
    name: "jinya-menu",
    mounted() {
      EventBus.$on(Events.navigation.navigated, () => {
        this.isOpen = false;
        this.isHover = false;
        this.selectedHeader = '';
      });
    },
    data() {
      return {
        isHover: false,
        isOpen: false,
        selectedHeader: ''
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-menu__header {
        text-align: center;
        flex: 1;
        font-size: 2rem;
        margin-top: auto;
        margin-bottom: auto;
        color: $primary-lighter;
    }
</style>