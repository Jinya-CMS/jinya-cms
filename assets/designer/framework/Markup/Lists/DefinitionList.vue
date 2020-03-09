<template>
    <dl :class="{'jinya-dl--horizontal': horizontal}">
        <template v-for="value in values">
            <dt :key="`title_${value.title}`" class="jinya-dl__dt">{{value.title}}</dt>
            <dd :key="`title_${value.value}`" class="jinya-dl__dd">{{value.value}}</dd>
        </template>
    </dl>
</template>

<script>
  export default {
    name: 'JinyaDefinitionList',
    props: {
      values: {
        type: Array,
        isRequired: true,
      },
      horizontal: Boolean,
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-dl--horizontal {
        display: flex;
        flex-wrap: wrap;

        dt {
            flex: 0 0 20%;
            width: 20%;
        }

        dd {
            flex: 0 0 80%;
            width: 80%;
        }
    }

    .jinya-dl__dt {
        margin: 0 0 0.5rem;
        font-weight: bold;
        padding: 0 0 0.5rem;
        border-bottom: 2px $primary solid;
        word-break: break-all;
    }

    .jinya-dl__dd {
        margin: 0 0 0.5rem;
        padding: 0 0 0.5rem;
        border-bottom: 2px $primary solid;
        word-break: break-all;
    }
</style>
