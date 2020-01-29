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

<style lang="scss">
    .jinya-menu-flyout__menu__section {
        margin: 1rem;

        h4 {
            color: $primary-light;
            font-size: 1.5rem;
            margin: 0;
            font-weight: 300;
            white-space: nowrap;
            padding: 0 0 0.5rem;
        }

        .jinya-menu-flyout__menu__items {
            list-style: none;
            padding: 0;
        }
    }
</style>
