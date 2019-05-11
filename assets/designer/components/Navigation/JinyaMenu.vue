<template>
  <!-- eslint-disable max-len -->
  <header class="jinya-menu">
    <jinya-menu-navbar @hamburger-click="isOpen = !isOpen">
      <span class="jinya-menu__header" v-if="title">{{title|jmessage}}</span>
      <jinya-menu-navbar-search-item v-if="$route.meta.searchEnabled"/>
    </jinya-menu-navbar>
    <jinya-menu-flyout>
      <jinya-menu-flyout-navbar :is-open="isOpen" slot="flyout-navbar">
        <jinya-menu-flyout-navbar-item :is-selected="selectedHeader === 'art'"
                                       @selected="selectHeader('art')"
                                       text="menu.designer.flyout.art.navbar"
                                       v-jinya-permission="ROLE_WRITER"/>
        <jinya-menu-flyout-navbar-item :is-selected="selectedHeader === 'static'"
                                       @selected="selectHeader('static')"
                                       text="menu.designer.flyout.static.navbar"
                                       v-jinya-permission="ROLE_WRITER"/>
        <jinya-menu-flyout-navbar-item :is-selected="selectedHeader === 'config'"
                                       @selected="selectHeader('config')"
                                       text="menu.designer.flyout.configuration.navbar"
                                       v-jinya-permission="ROLE_ADMIN"/>
        <jinya-menu-flyout-navbar-item :is-selected="selectedHeader === 'maintenance'"
                                       @selected="selectHeader('maintenance')"
                                       text="menu.designer.flyout.maintenance.navbar"
                                       v-jinya-permission="ROLE_SUPER_ADMIN"/>
        <jinya-menu-flyout-navbar-item :is-selected="selectedHeader === 'my-jinya'"
                                       @selected="selectHeader('my-jinya')"
                                       text="menu.designer.flyout.my_jinya.navbar"/>
        <jinya-menu-flyout-navbar-item :is-selected="selectedHeader === 'support'"
                                       @selected="selectHeader('support')"
                                       text="menu.designer.flyout.support.navbar"/>
      </jinya-menu-flyout-navbar>
      <jinya-menu-flyout-menu :is-open="isOpen && selectedHeader === 'art'" slot="flyout-menus"
                              v-jinya-permission="ROLE_WRITER">
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.art.sections.artworks.header"
                                        v-jinya-permission="ROLE_WRITER">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.art.sections.artworks.saved_in_jinya"
                                       to="Art.Artworks.SavedInJinya.Overview"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.art.sections.artworks.saved_external"
                                       to="Art.Artworks.SavedExternal.Overview"
                                       v-if="false"/>
        </jinya-menu-flyout-menu-section>
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.art.sections.videos.header"
                                        v-jinya-permission="ROLE_WRITER">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.art.sections.videos.saved_in_jinya"
                                       to="Art.Videos.SavedInJinya.Overview"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.art.sections.videos.saved_on_youtube"
                                       to="Art.Videos.SavedOnYoutube.Overview"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.art.sections.videos.saved_on_vimeo"
                                       to="Art.Videos.SavedOnVimeo.Overview"
                                       v-if="false"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.art.sections.videos.saved_on_newgrounds"
                                       to="Art.Videos.SavedOnNewgrounds.Overview"
                                       v-if="false"/>
        </jinya-menu-flyout-menu-section>
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.art.sections.galleries.header"
                                        v-jinya-permission="ROLE_WRITER">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.art.sections.galleries.artwork_galleries"
                                       to="Art.Galleries.Art.Overview"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.art.sections.galleries.video_galleries"
                                       to="Art.Galleries.Video.Overview"/>
        </jinya-menu-flyout-menu-section>
      </jinya-menu-flyout-menu>
      <jinya-menu-flyout-menu :is-open="isOpen && selectedHeader === 'static'" slot="flyout-menus"
                              v-jinya-permission="ROLE_WRITER">
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.static.sections.pages.header"
                                        v-jinya-permission="ROLE_WRITER">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.static.sections.pages.saved_in_jinya"
                                       to="Static.Pages.SavedInJinya.Overview"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.static.sections.pages.saved_external"
                                       to="Static.Pages.SavedExternal.Overview"
                                       v-if="false"/>
        </jinya-menu-flyout-menu-section>
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.static.sections.forms.header"
                                        v-jinya-permission="ROLE_WRITER">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.static.sections.forms.forms"
                                       to="Static.Forms.Forms.Overview"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.static.sections.forms.requests"
                                       to="Static.Forms.Requests.Overview"
                                       v-if="false"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.static.sections.forms.email_templates"
                                       to="Static.Forms.EmailTemplates.Overview"
                                       v-if="false"/>
        </jinya-menu-flyout-menu-section>
      </jinya-menu-flyout-menu>
      <jinya-menu-flyout-menu :is-open="isOpen && selectedHeader === 'config'" slot="flyout-menus"
                              v-jinya-permission="ROLE_ADMIN">
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.configuration.sections.general.header"
                                        v-jinya-permission="ROLE_SUPER_ADMIN">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.configuration.sections.general.artists"
                                       to="Configuration.General.Artists.Overview"/>
        </jinya-menu-flyout-menu-section>
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.configuration.sections.frontend.header"
                                        v-jinya-permission="ROLE_ADMIN">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.configuration.sections.frontend.themes"
                                       to="Configuration.Frontend.Theme.Overview"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.configuration.sections.frontend.menus"
                                       to="Configuration.Frontend.Menu.Overview"/>
        </jinya-menu-flyout-menu-section>
      </jinya-menu-flyout-menu>
      <jinya-menu-flyout-menu :is-open="isOpen && selectedHeader === 'maintenance'" slot="flyout-menus"
                              v-jinya-permission="ROLE_SUPER_ADMIN">
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.system.header"
                                        v-jinya-permission="ROLE_SUPER_ADMIN">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.system.updates"
                                       to="Maintenance.System.Updates"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.system.environment"
                                       to="Maintenance.System.Environment"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.system.cache"
                                       to="Maintenance.System.Cache"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.system.version"
                                       to="Maintenance.System.Version"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.system.php"
                                       to="Maintenance.System.PHP"/>
        </jinya-menu-flyout-menu-section>
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.database.header"
                                        v-jinya-permission="ROLE_SUPER_ADMIN">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.database.mysql_information"
                                       to="Maintenance.Database.MySQL"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.database.database_tool"
                                       to="Maintenance.Database.Tool"/>
        </jinya-menu-flyout-menu-section>
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.maintenance.sections.diagnosis.header"
                                        v-jinya-permission="ROLE_SUPER_ADMIN">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.diagnosis.application_log"
                                       to="Maintenance.Diagnosis.ApplicationLog.Overview"/>
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.maintenance.sections.diagnosis.access_log"
                                       to="Maintenance.Diagnosis.AccessLog.Overview"/>
        </jinya-menu-flyout-menu-section>
      </jinya-menu-flyout-menu>
      <jinya-menu-flyout-menu :is-open="isOpen && selectedHeader === 'my-jinya'" slot="flyout-menus">
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
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.my_jinya.sections.two_factor.header">
          <jinya-menu-flyout-menu-item text="menu.designer.flyout.my_jinya.sections.two_factor.known_devices"
                                       to="MyJinya.TwoFactor.KnownDevices"/>
        </jinya-menu-flyout-menu-section>
      </jinya-menu-flyout-menu>
      <jinya-menu-flyout-menu :is-open="isOpen && selectedHeader === 'support'" slot="flyout-menus">
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.support.sections.problems.header">
          <jinya-menu-flyout-menu-item :direct-link="true" :navigate="false" @selected="$emit('show-bug')"
                                       text="menu.designer.flyout.support.sections.problems.bug"
                                       to=""/>
          <jinya-menu-flyout-menu-item :direct-link="true" :navigate="false" @selected="$emit('show-feature')"
                                       text="menu.designer.flyout.support.sections.problems.feature"
                                       to=""/>
        </jinya-menu-flyout-menu-section>
        <jinya-menu-flyout-menu-section header="menu.designer.flyout.support.sections.like.header">
          <jinya-menu-flyout-menu-item :direct-link="true" :navigate="false" @selected="$emit('show-like')"
                                       text="menu.designer.flyout.support.sections.like.like"
                                       to=""/>
          <jinya-menu-flyout-menu-item :direct-link="true" text="menu.designer.flyout.support.sections.like.dont_like"
                                       to="mailto:developers@jinya.de"/>
        </jinya-menu-flyout-menu-section>
      </jinya-menu-flyout-menu>
    </jinya-menu-flyout>
  </header>
</template>

<script>
  import JinyaMenuNavbar from '@/framework/Markup/Menu/Navbar/JinyaMenuNavbar';
  import JinyaMenuNavbarItem from '@/framework/Markup/Menu/Navbar/JinyaMenuNavbarItem';
  import JinyaMenuFlyout from '@/framework/Markup/Menu/Flyout/JinyaMenuFlyout';
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
      JinyaMenuFlyout,
      JinyaMenuNavbarItem,
      JinyaMenuNavbar,
    },
    methods: {
      selectHeader(name) {
        this.selectedHeader = name;
        this.isOpen = true;
        EventBus.$on(Events.navigation.navigated, this.navigated);
      },
      async logout() {
        await logout();
      },
      navigated() {
        this.isOpen = false;
        this.selectedHeader = '';
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
        isOpen: false,
        selectedHeader: '',
        title: this.$route.meta.title,
      };
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-menu__header {
    text-align: center;
    flex: 1;
    font-size: 2rem;
    margin-top: auto;
    margin-bottom: auto;
    color: $primary-lighter;
  }
</style>
