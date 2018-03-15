<template>
    <ul class="jinya-menu__navbar">
        <li class="jinya-menu__hamburger" @click="$emit('hamburger-click')" @mouseover="$emit('hamburger-mouseover')"
            @mouseout="$emit('hamburger-mouseout')">
            <span></span>
            <span></span>
            <span></span>
        </li>
        <slot/>
    </ul>
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

        li {
            list-style: none;
            margin: 0;
            background: $primary;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 15px;
            color: white;

            a {
                display: block;
                font-variant: all-small-caps;
                cursor: pointer;
            }

            &:hover {
                background: $white;

                a {
                    color: $primary;
                }
            }
        }

        .jinya-menu__hamburger {
            background: $primary;
            width: 60px;
            padding: 10px;
            cursor: pointer;

            &:hover {
                background: $white;

                span {
                    background: $primary;
                }
            }

            span {
                height: 5px;
                width: 80%;
                background: $white;
                margin: 2.5px 10%;
                display: block;
            }
        }
    }
</style>