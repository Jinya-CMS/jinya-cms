<template>
    <header class="jinya-menu">
        <jinya-menu-navbar @hamburger-click="isOpen = !isOpen">
            <span class="jinya-menu__header" v-if="title">{{title|jmessage}}</span>
            <jinya-menu-navbar-search-item v-if="$route.meta.searchEnabled"/>
        </jinya-menu-navbar>
        <jinya-menu-flyout>
            <jinya-menu-flyout-navbar slot="flyout-navbar" :is-open="isOpen">
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.art.navbar"
                                               :is-selected="selectedHeader === 'art'"
                                               @selected="selectHeader('art')"
                                               v-jinya-permission="ROLE_WRITER"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.static.navbar"
                                               :is-selected="selectedHeader === 'static'"
                                               @selected="selectHeader('static')"
                                               v-jinya-permission="ROLE_WRITER"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.configuration.navbar"
                                               :is-selected="selectedHeader === 'config'"
                                               @selected="selectHeader('config')"
                                               v-jinya-permission="ROLE_ADMIN"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.maintenance.navbar"
                                               :is-selected="selectedHeader === 'maintenance'"
                                               @selected="selectHeader('maintenance')"
                                               v-jinya-permission="ROLE_SUPER_ADMIN"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.my_jinya.navbar"
                                               :is-selected="selectedHeader === 'my-jinya'"
                                               @selected="selectHeader('my-jinya')"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.support.navbar"
                                               :is-selected="selectedHeader === 'support'"
                                               @selected="selectHeader('support')"/>
            </jinya-menu-flyout-navbar>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'art'"
                                    v-jinya-permission="ROLE_WRITER">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.art.sections.artworks.header"
                                                v-jinya-permission="ROLE_WRITER">
                    <jinya-menu-flyout-menu-item to="Art.Artworks.SavedInJinya.Overview"
                                                 text="menu.designer.flyout.art.sections.artworks.saved_in_jinya"/>
                    <jinya-menu-flyout-menu-item to="Art.Artworks.SavedExternal.Overview" v-if="false"
                                                 text="menu.designer.flyout.art.sections.artworks.saved_external"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.art.sections.videos.header"
                                                v-jinya-permission="ROLE_WRITER">
                    <jinya-menu-flyout-menu-item to="Art.Videos.SavedInJinya.Overview"
                                                 text="menu.designer.flyout.art.sections.videos.saved_in_jinya"/>
                    <jinya-menu-flyout-menu-item to="Art.Videos.SavedOnYoutube.Overview"
                                                 text="menu.designer.flyout.art.sections.videos.saved_on_youtube"/>
                    <jinya-menu-flyout-menu-item to="Art.Videos.SavedOnVimeo.Overview" v-if="false"
                                                 text="menu.designer.flyout.art.sections.videos.saved_on_vimeo"/>
                    <jinya-menu-flyout-menu-item to="Art.Videos.SavedOnNewgrounds.Overview" v-if="false"
                                                 text="menu.designer.flyout.art.sections.videos.saved_on_newgrounds"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.art.sections.galleries.header"
                                                v-jinya-permission="ROLE_WRITER">
                    <jinya-menu-flyout-menu-item to="Art.Galleries.Art.Overview"
                                                 text="menu.designer.flyout.art.sections.galleries.artwork_galleries"/>
                    <jinya-menu-flyout-menu-item to="Art.Galleries.Video.Overview"
                                                 text="menu.designer.flyout.art.sections.galleries.video_galleries"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'static'"
                                    v-jinya-permission="ROLE_WRITER">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.static.sections.pages.header"
                                                v-jinya-permission="ROLE_WRITER">
                    <jinya-menu-flyout-menu-item to="Static.Pages.SavedInJinya.Overview"
                                                 text="menu.designer.flyout.static.sections.pages.saved_in_jinya"/>
                    <jinya-menu-flyout-menu-item to="Static.Pages.SavedExternal.Overview" v-if="false"
                                                 text="menu.designer.flyout.static.sections.pages.saved_external"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.static.sections.forms.header"
                                                v-jinya-permission="ROLE_WRITER">
                    <jinya-menu-flyout-menu-item to="Static.Forms.Forms.Overview"
                                                 text="menu.designer.flyout.static.sections.forms.forms"/>
                    <jinya-menu-flyout-menu-item to="Static.Forms.Requests.Overview" v-if="false"
                                                 text="menu.designer.flyout.static.sections.forms.requests"/>
                    <jinya-menu-flyout-menu-item to="Static.Forms.EmailTemplates.Overview" v-if="false"
                                                 text="menu.designer.flyout.static.sections.forms.email_templates"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'config'"
                                    v-jinya-permission="ROLE_ADMIN">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.configuration.sections.general.header"
                                                v-jinya-permission="ROLE_SUPER_ADMIN">
                    <jinya-menu-flyout-menu-item to="Configuration.General.Artists.Overview"
                                                 text="menu.designer.flyout.configuration.sections.general.artists"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.configuration.sections.frontend.header"
                                                v-jinya-permission="ROLE_ADMIN">
                    <jinya-menu-flyout-menu-item to="Configuration.Frontend.Theme.Overview"
                                                 text="menu.designer.flyout.configuration.sections.frontend.themes"/>
                    <jinya-menu-flyout-menu-item to="Configuration.Frontend.Menu.Overview"
                                                 text="menu.designer.flyout.configuration.sections.frontend.menus"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'maintenance'"
                                    v-jinya-permission="ROLE_SUPER_ADMIN">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.system.header"
                                                v-jinya-permission="ROLE_SUPER_ADMIN">
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
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.database.header"
                                                v-jinya-permission="ROLE_SUPER_ADMIN">
                    <jinya-menu-flyout-menu-item to="Maintenance.Database.MySQL"
                                                 text="menu.designer.flyout.maintenance.sections.database.mysql_information"/>
                    <jinya-menu-flyout-menu-item to="Maintenance.Database.Tool"
                                                 text="menu.designer.flyout.maintenance.sections.database.database_tool"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.diagnosis.header"
                                                v-jinya-permission="ROLE_SUPER_ADMIN">
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
                    <jinya-menu-flyout-menu-item to="MyJinya.Account.ApiKeys"
                                                 text="menu.designer.flyout.my_jinya.sections.account.api_keys"/>
                    <jinya-menu-flyout-menu-item @selected="logout"
                                                 text="menu.designer.flyout.my_jinya.sections.account.logout"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.my_jinya.sections.two_factor.header">
                    <jinya-menu-flyout-menu-item to="MyJinya.TwoFactor.KnownDevices"
                                                 text="menu.designer.flyout.my_jinya.sections.two_factor.known_devices"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'support'">
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.support.sections.problems.header">
                    <jinya-menu-flyout-menu-item @selected="$emit('show-bug')" :direct-link="true" to=""
                                                 :navigate="false"
                                                 text="menu.designer.flyout.support.sections.problems.bug"/>
                    <jinya-menu-flyout-menu-item @selected="$emit('show-feature')" :direct-link="true" to=""
                                                 :navigate="false"
                                                 text="menu.designer.flyout.support.sections.problems.feature"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="menu.designer.flyout.support.sections.like.header">
                    <jinya-menu-flyout-menu-item @selected="$emit('show-like')" :direct-link="true" to=""
                                                 :navigate="false"
                                                 text="menu.designer.flyout.support.sections.like.like"/>
                    <jinya-menu-flyout-menu-item to="mailto:developers@jinya.de" :direct-link="true"
                                                 text="menu.designer.flyout.support.sections.like.dont_like"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
        </jinya-menu-flyout>
    </header>
</template>

<script>
  import JinyaMenuNavbar from "@/framework/Markup/Menu/Navbar/JinyaMenuNavbar";
  import JinyaMenuNavbarItem from "@/framework/Markup/Menu/Navbar/JinyaMenuNavbarItem";
  import JinyaMenuFlyout from "@/framework/Markup/Menu/Flyout/JinyaMenuFlyout";
  import JinyaMenuFlyoutNavbarItem from "@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutNavbarItem";
  import JinyaMenuFlyoutNavbar from "@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutNavbar";
  import JinyaMenuFlyoutMenu from "@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutMenu";
  import JinyaMenuFlyoutMenuSection from "@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutMenuSection";
  import JinyaMenuFlyoutMenuItem from "@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutMenuItem";
  import JinyaMenuNavbarSearchItem from "@/framework/Markup/Menu/Navbar/JinyaMenuNavbarSearchItem";
  import EventBus from "../../framework/Events/EventBus";
  import Events from "../../framework/Events/Events";
  import {logout} from "@/security/Authentication";

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
      },
      async logout() {
        await logout();
      }
    },
    name: "jinya-menu",
    mounted() {
      EventBus.$on(Events.navigation.navigated, () => {
        this.isOpen = false;
        this.selectedHeader = '';
        this.title = this.$route.meta.title || this.title;
      });
      EventBus.$on(Events.header.change, header => this.title = header);
    },
    data() {
      return {
        isOpen: false,
        selectedHeader: '',
        title: this.$route.meta.title
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