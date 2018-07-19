<template>
    <div class="jinya-menu-flyout__menu__section">
        <h4 v-jinya-message="header"></h4>
        <ul class="jinya-menu-flyout__menu__items">
            <slot/>
        </ul>
    </div>
</template>

<script>
  export default {
    name: 'jinya-menu-flyout-menu-section',
    props: {
      header: {
        type: String,
        required: true,
      },
    },
  };
</script>

<style scoped lang="scss">
    .jinya-menu-flyout__menu__section {
        margin: 1em 2.5em 3em;

        &:first-child {
            margin-left: 1.3em;
        }

        &:last-child {
            margin-right: 0;
        }

        h4 {
            color: $primary-lighter;
            font-size: 1.7rem;
            font-weight: 200;
            margin: 0;
            padding: 0;
        }

        .jinya-menu-flyout__menu__items {
            list-style: none;
            padding: 0;
            margin: 1em 0 0;
        }
    }
</style>
