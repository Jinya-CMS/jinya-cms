<template>
    <div class="jinya-menu__navbar">
        <div class="jinya-menu__hamburger" @click="$emit('hamburger-click')" @mouseover="$emit('hamburger-mouseover')"
            @mouseout="$emit('hamburger-mouseout')">
            <span class="jinya-menu__hamburger__bar"></span>
            <span class="jinya-menu__hamburger__bar"></span>
            <span class="jinya-menu__hamburger__bar"></span>
        </div>
        <slot/>
    </div>
</template>

<script>
  export default {
    name: "jinya-menu-navbar"
  }
</script>

<style scoped lang="scss">
    @import "designer";

    .jinya-menu__navbar {
        width: 100%;
        display: flex;
        margin: 0;
        padding: 0;
        background: $primary;
        height: 60px;

        .jinya-menu__hamburger {
            background: $primary;
            width: 60px;
            cursor: pointer;
            margin: 0;
            background: $primary;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 10px;
            color: white;

            &:hover {
                background: $white;

                .jinya-menu__hamburger__bar {
                    background: $primary;
                }
            }

            .jinya-menu__hamburger__bar {
                height: 5px;
                width: 80%;
                background: $white;
                margin: 2.5px 10%;
                display: block;
            }
        }
    }
</style>