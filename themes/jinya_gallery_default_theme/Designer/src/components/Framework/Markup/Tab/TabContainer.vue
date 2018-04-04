<template>
    <div class="jinya-tab">
        <nav class="jinya-tab__list">
            <template v-for="item in items">
                <a class="jinya-tab__link" @click="select(item)" :class="{'is--selected': item.name === selectedItem}">
                    {{item.title}}
                </a>
            </template>
        </nav>
        <div class="jinya-tab__container">
            <slot/>
        </div>
    </div>
</template>

<script>
  export default {
    name: "jinya-tab-container",
    props: {
      items: {
        type: Array,
        required: true,
        validate(input) {
          return input.every(value => value.title && value.name);
        }
      }
    },
    data() {
      return {
        selectedItem: ''
      };
    },
    mounted() {
      const selectedItems = this.items.filter(item => item.isSelected);
      const selectedItem = selectedItems.length > 0 ? selectedItems[0] : this.items[0];

      this.select(selectedItem);
    },
    methods: {
      select(item) {
        this.selectedItem = item.name;
        this.$emit('select', item.name);
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-tab {
        width: 100%;

        .jinya-tab__list {
            width: 100%;
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
            margin-bottom: -2px;

            .jinya-tab__link {
                flex: 0 1 auto;
                padding: 0.5em 1em 0;
                color: $gray-500;
                font-size: 1.4em;
                cursor: pointer;
                border: 2px solid transparent;
                transition: color 0.3s, background 0.3s, border 0.3s;

                &.is--selected {
                    color: $primary;
                    border: 2px solid $primary;
                    border-bottom-color: $white;
                    background: $white;
                }
            }
        }

        .jinya-tab__container {
            padding-top: 1em;
            border-top: 2px solid $primary;
        }
    }
</style>