<template>
    <table class="jinya-table">
        <thead class="jinya-table__head">
        <tr>
            <th :key="header.name" class="jinya-table__column-header" v-for="header in headers">{{header.title}}</th>
        </tr>
        </thead>
        <tbody class="jinya-table__body">
        <tr :class="{'is--selected': JSON.stringify(row) === JSON.stringify(selectedRow)}" :key="JSON.stringify(row)"
            @click="$emit('selected', row)" class="jinya-table__row" v-for="row in rows">
            <td :key="header.name + JSON.stringify(row[header.name])" class="jinya-table__cell"
                v-for="header in headers">
                <template v-if="header.template">{{header.template(row)}}</template>
                <template v-else>{{row[header.name]}}</template>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
  export default {
    name: 'Table',
    props: {
      headers: {
        isRequired: true,
        type: Array,
      },
      rows: {
        isRequired: true,
        type: Array,
      },
      selectedRow: {
        isRequired: true,
        type: Object,
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-table {
        width: 100%;
        border-spacing: 0;
        border-collapse: collapse;
    }

    .jinya-table__column-header {
        font-weight: bold;
        border-bottom: 2px solid $primary-darker;
        text-align: left;
        color: $primary-darker;
        padding: 0.75rem 0.75rem 0.5rem;
    }

    .jinya-table__cell {
        padding: 0.75rem;
    }

    .jinya-table__row {
        color: $black;
        cursor: pointer;
        background: $white;
        transition: all 0.3s;

        &:nth-child(2n) {
            background: $primary-lightest;
        }

        &:hover {
            background: $primary-lighter;
        }

        &.is--selected {
            background: $secondary;
        }
    }
</style>
