<template>
    <div id="app">
        <jinya-menu v-if="$route.name !== 'Login'"/>
        <router-view/>
    </div>
</template>

<script>
  import JinyaMenu from "@/components/Framework/Markup/Menu/JinyaMenu";

  export default {
    components: {JinyaMenu},
    name: 'App'
  }
</script>

<style lang="scss">
    #app {
        height: 100%;
        width: 100%;
        overflow: auto;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: $font-family;
        margin: 0;
        overflow: auto;
    }

    a {
        color: $primary;

        &:hover {
            color: $primary-darker;
        }
    }

    h1, h2, h3, h4, h5, h6 {
        color: $primary;
        font-family: $font-family-heading;
    }

    .sr--only {
        display: none;

        @media speech {
            display: inline-block;
        }
    }
</style>
