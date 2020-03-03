<template>
    <header class="jinya-menu">
        <jinya-menu-navbar>
            <span class="jinya-menu__header" v-if="title">{{title|jmessage}}</span>
            <jinya-menu-flyout-navbar>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.content.navbar"
                                               v-jinya-permission="writer">
                    <jinya-menu-flyout-menu v-jinya-permission="writer">
                        <jinya-menu-flyout-menu-section header="menu.designer.flyout.content.sections.media.header"
                                                        v-jinya-permission="writer">
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.content.sections.media.files"
                                                         to="Media.Files.FileBrowser"/>
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.content.sections.media.galleries"
                                                         to="Media.Galleries.Overview"/>
                        </jinya-menu-flyout-menu-section>
                        <jinya-menu-flyout-menu-section header="menu.designer.flyout.content.sections.pages.header"
                                                        v-jinya-permission="writer">
                            <jinya-menu-flyout-menu-item
                                text="menu.designer.flyout.content.sections.pages.saved_in_jinya"
                                to="Static.Pages.SavedInJinya.Overview"/>
                            <jinya-menu-flyout-menu-item
                                text="menu.designer.flyout.content.sections.pages.segment_pages"
                                to="Static.Pages.Segment.Overview"/>
                        </jinya-menu-flyout-menu-section>
                        <jinya-menu-flyout-menu-section header="menu.designer.flyout.content.sections.forms.header"
                                                        v-jinya-permission="writer">
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.content.sections.forms.forms"
                                                         to="Static.Forms.Forms.Overview"/>
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.content.sections.forms.messages"
                                                         to="Static.Forms.Messages.Overview"
                                                         v-jinya-permission="writer"/>
                            <jinya-menu-flyout-menu-item
                                text="menu.designer.flyout.content.sections.forms.email_templates"
                                to="Static.Forms.EmailTemplates.Overview"
                                v-if="false"/>
                        </jinya-menu-flyout-menu-section>
                    </jinya-menu-flyout-menu>
                </jinya-menu-flyout-navbar-item>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.configuration.navbar"
                                               v-jinya-permission="admin">
                    <jinya-menu-flyout-menu v-jinya-permission="admin">
                        <jinya-menu-flyout-menu-section
                            header="menu.designer.flyout.configuration.sections.general.header"
                            v-jinya-permission="superAdmin">
                            <jinya-menu-flyout-menu-item
                                text="menu.designer.flyout.configuration.sections.general.artists"
                                to="Configuration.General.Artists.Overview"/>
                        </jinya-menu-flyout-menu-section>
                        <jinya-menu-flyout-menu-section
                            header="menu.designer.flyout.configuration.sections.frontend.header"
                            v-jinya-permission="admin">
                            <jinya-menu-flyout-menu-item
                                text="menu.designer.flyout.configuration.sections.frontend.themes"
                                to="Configuration.Frontend.Theme.Overview"/>
                            <jinya-menu-flyout-menu-item
                                text="menu.designer.flyout.configuration.sections.frontend.menus"
                                to="Configuration.Frontend.Menu.Overview"/>
                        </jinya-menu-flyout-menu-section>
                    </jinya-menu-flyout-menu>
                </jinya-menu-flyout-navbar-item>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.maintenance.navbar"
                                               v-jinya-permission="superAdmin">
                    <jinya-menu-flyout-menu v-jinya-permission="superAdmin">
                        <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.system.header"
                                                        v-jinya-permission="superAdmin">
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.system.updates"
                                                         to="Maintenance.System.Updates"/>
                            <jinya-menu-flyout-menu-item
                                text="menu.designer.flyout.maintenance.sections.system.environment"
                                to="Maintenance.System.Environment"/>
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.system.cache"
                                                         to="Maintenance.System.Cache"/>
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.system.version"
                                                         to="Maintenance.System.Version"/>
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.system.php"
                                                         to="Maintenance.System.PHP"/>
                        </jinya-menu-flyout-menu-section>
                        <jinya-menu-flyout-menu-section
                            header="menu.designer.flyout.maintenance.sections.database.header"
                            v-jinya-permission="superAdmin">
                            <jinya-menu-flyout-menu-item
                                text="menu.designer.flyout.maintenance.sections.database.database_tool"
                                to="Maintenance.Database.Tool"/>
                        </jinya-menu-flyout-menu-section>
                    </jinya-menu-flyout-menu>
                </jinya-menu-flyout-navbar-item>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.my_jinya.navbar">
                    <jinya-menu-flyout-menu>
                        <jinya-menu-flyout-menu-section header="menu.designer.flyout.my_jinya.sections.account.header">
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.my_jinya.sections.account.profile"
                                                         to="MyJinya.Account.Profile"/>
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.my_jinya.sections.account.password"
                                                         to="MyJinya.Account.Password"/>
                            <jinya-menu-flyout-menu-item text="menu.designer.flyout.my_jinya.sections.account.api_keys"
                                                         to="MyJinya.Account.ApiKeys"/>
                            <jinya-menu-flyout-menu-item @selected="logout"
                                                         text="menu.designer.flyout.my_jinya.sections.account.logout"/>
                        </jinya-menu-flyout-menu-section>
                        <jinya-menu-flyout-menu-section
                            header="menu.designer.flyout.my_jinya.sections.two_factor.header">
                            <jinya-menu-flyout-menu-item
                                text="menu.designer.flyout.my_jinya.sections.two_factor.known_devices"
                                to="MyJinya.TwoFactor.KnownDevices"/>
                        </jinya-menu-flyout-menu-section>
                    </jinya-menu-flyout-menu>
                </jinya-menu-flyout-navbar-item>
                <jinya-menu-flyout-navbar-item text="menu.designer.flyout.support.navbar">
                    <jinya-menu-flyout-menu>
                        <jinya-menu-flyout-menu-section header="menu.designer.flyout.support.sections.problems.header">
                            <jinya-menu-flyout-menu-item :direct-link="true" :navigate="false"
                                                         @selected="$emit('show-bug')"
                                                         text="menu.designer.flyout.support.sections.problems.bug"
                                                         to=""/>
                            <jinya-menu-flyout-menu-item :direct-link="true" :navigate="false"
                                                         @selected="$emit('show-feature')"
                                                         text="menu.designer.flyout.support.sections.problems.feature"
                                                         to=""/>
                        </jinya-menu-flyout-menu-section>
                        <jinya-menu-flyout-menu-section header="menu.designer.flyout.support.sections.like.header">
                            <jinya-menu-flyout-menu-item :direct-link="true" :navigate="false"
                                                         @selected="$emit('show-like')"
                                                         text="menu.designer.flyout.support.sections.like.like"
                                                         to=""/>
                            <jinya-menu-flyout-menu-item :direct-link="true"
                                                         text="menu.designer.flyout.support.sections.like.dont_like"
                                                         to="mailto:developers@jinya.de"/>
                        </jinya-menu-flyout-menu-section>
                    </jinya-menu-flyout-menu>
                </jinya-menu-flyout-navbar-item>
            </jinya-menu-flyout-navbar>
            <jinya-menu-navbar-search-item v-if="$route.meta.searchEnabled"/>
        </jinya-menu-navbar>
    </header>
</template>

<script>
  import JinyaMenuNavbar from '@/framework/Markup/Menu/Navbar/JinyaMenuNavbar';
  import JinyaMenuFlyoutNavbarItem from '@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutNavbarItem';
  import JinyaMenuFlyoutNavbar from '@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutNavbar';
  import JinyaMenuFlyoutMenu from '@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutMenu';
  import JinyaMenuFlyoutMenuSection from '@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutMenuSection';
  import JinyaMenuFlyoutMenuItem from '@/framework/Markup/Menu/Flyout/JinyaMenuFlyoutMenuItem';
  import JinyaMenuNavbarSearchItem from '@/framework/Markup/Menu/Navbar/JinyaMenuNavbarSearchItem';
  import EventBus from '../../framework/Events/EventBus';
  import Events from '../../framework/Events/Events';
  import { logout } from '@/security/Authentication';

  export default {
    components: {
      JinyaMenuNavbarSearchItem,
      JinyaMenuFlyoutMenuItem,
      JinyaMenuFlyoutMenuSection,
      JinyaMenuFlyoutMenu,
      JinyaMenuFlyoutNavbar,
      JinyaMenuFlyoutNavbarItem,
      JinyaMenuNavbar,
    },
    methods: {
      async logout() {
        await logout();
      },
      navigated() {
        this.title = this.$route.meta.title || this.title;
        EventBus.$off(Events.navigation.navigated, this.navigated);
      },
    },
    name: 'jinya-menu',
    mounted() {
      EventBus.$on(Events.header.change, (header) => {
        this.title = header;
      });
    },
    data() {
      return {
        title: this.$route.meta.title,
      };
    },
  };
</script>

<style lang="scss">
    .jinya-menu__header {
        font-size: 1.2rem;
        font-weight: bold;
        margin-top: auto;
        margin-bottom: auto;
        color: $primary-darkest;
        display: block;
        @include breakpoint-desktop {
            min-width: calc(10vw - 2rem);
        }
        min-width: 15rem;
        padding-left: 1rem;
        box-sizing: border-box;
    }
</style>
