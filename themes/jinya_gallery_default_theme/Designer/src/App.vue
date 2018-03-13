<template>
    <div id="app">
        <router-view/>
    </div>
</template>

<script>
  export default {
    name: 'App'
  }
</script>

<style lang="scss">
    //noinspection CssUnknownTarget
    @import "designer";

    #app {
        height: 100%;
        width: 100%;
        overflow: auto;
    }

    body {
        font-family: $font-family;
        box-sizing: border-box;
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
</style>
