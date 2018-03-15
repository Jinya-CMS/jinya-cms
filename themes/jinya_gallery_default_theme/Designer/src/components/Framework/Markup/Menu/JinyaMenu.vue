<template>
    <header class="jinya-menu">
        <jinya-menu-navbar @hamburger-mouseover="isHover = true" @hamburger-mouseout="isHover = false"
                           @hamburger-click="isOpen = !isOpen">
        </jinya-menu-navbar>
        <jinya-menu-flyout @mouseover.native="isHover = true" @mouseout.native="isHover = false">
            <jinya-menu-flyout-navbar slot="flyout-navbar" :is-open="isOpen || isHover">
                <jinya-menu-flyout-navbar-item text="menu.designer.art" to="Login"
                                               :is-selected="selectedHeader === 'art'"
                                               @selected="selectHeader('art')"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.static" to="Login"
                                               :is-selected="selectedHeader === 'static'"
                                               @selected="selectHeader('static')"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.configuration" to="Login"
                                               :is-selected="selectedHeader === 'config'"
                                               @selected="selectHeader('config')"/>
                <jinya-menu-flyout-navbar-item text="menu.designer.maintenance" to="Login"
                                               :is-selected="selectedHeader === 'maintenance'"
                                               @selected="selectHeader('maintenance')"/>
            </jinya-menu-flyout-navbar>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'art'">
                <jinya-menu-flyout-menu-section header="Kunstwerke">
                    <jinya-menu-flyout-menu-item to="Login" text="In Jinya CMS gespeichert"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Extern gespeichert"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="Videos">
                    <jinya-menu-flyout-menu-item to="Login" text="In Jinya CMS gespeichert"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Auf Youtube gespeichert"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Auf Vimeo gespeichert"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="Galerien">
                    <jinya-menu-flyout-menu-item to="Login" text="Kunstgalerien"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Videogalerien"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'static'">
                <jinya-menu-flyout-menu-section header="Seiten">
                    <jinya-menu-flyout-menu-item to="Login" text="In Jinya CMS gespeichert"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Extern gespeichert"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="Formulare">
                    <jinya-menu-flyout-menu-item to="Login" text="Formulare"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Anfragen"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Emailtemplates"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'config'">
                <jinya-menu-flyout-menu-section header="Allgemein">
                    <jinya-menu-flyout-menu-item to="Login" text="Künstler"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="Frontend">
                    <jinya-menu-flyout-menu-item to="Login" text="Themes"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Menüs"/>
                </jinya-menu-flyout-menu-section>
            </jinya-menu-flyout-menu>
            <jinya-menu-flyout-menu slot="flyout-menus" :is-open="isOpen && selectedHeader === 'maintenance'">
                <jinya-menu-flyout-menu-section header="System">
                    <jinya-menu-flyout-menu-item to="Login" text="Updates"/>
                    <jinya-menu-flyout-menu-item to="Login" text=".env Einstellungen"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Cache"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Jinya CMS Version"/>
                    <jinya-menu-flyout-menu-item to="Login" text="PHP Info"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="Datenbank">
                    <jinya-menu-flyout-menu-item to="Login" text="MySQL Informationen"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Adminer"/>
                </jinya-menu-flyout-menu-section>
                <jinya-menu-flyout-menu-section header="Diagnose">
                    <jinya-menu-flyout-menu-item to="Login" text="Anwendungsprotokoll"/>
                    <jinya-menu-flyout-menu-item to="Login" text="Zugriffsprotokoll"/>
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

  export default {
    components: {
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
    data() {
      return {
        'isHover': false,
        'isOpen': false,
        'selectedHeader': ''
      }
    }
  }
</script>

<style scoped lang="scss">
</style>