<template>
    <div class="jinya-database-tool">
        <h1>{{'maintenance.database.tool.title'|jmessage}}</h1>
        <monaco-editor class="jinya-database-tool__editor" language="mysql" v-model="query"/>
        <jinya-button @click="executeQuery"
                      class="jinya-button--execute"
                      is-primary
                      label="maintenance.database.tool.execute"/>
        <div class="jinya-database-tool__result" v-if="executed.length > 0">
            <jinya-tab-container :selected-item="selectedStatement"
                                 :items="statements"
                                 @select="selectedStatement = $event">
                <jinya-tab :is-selected="statement.name === selectedStatement"
                           :key="statement.name"
                           v-for="statement in statements">
                    <jinya-table :headers="headers(statement.name)"
                                 :rows="rows(statement.name)"
                                 v-if="isTableResult(statement.name)"/>
                    <jinya-message :message="message(statement.name)" state="info" v-else/>
                </jinya-tab>
            </jinya-tab-container>
        </div>
    </div>
</template>

<script>
  import MonacoEditor from 'vue-monaco';
  import JinyaButton from '@/framework/Markup/Button';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaTabContainer from '@/framework/Markup/Tab/TabContainer';
  import JinyaTable from '@/framework/Markup/Table/Table';
  import JinyaMessage from '@/framework/Markup/Validation/Message';
  import JinyaTab from '@/framework/Markup/Tab/Tab';
  import isNumber from 'lodash/isNumber';
  import Translator from '@/framework/i18n/Translator';

  export default {
    name: 'Tool',
    components: {
      JinyaTab,
      JinyaMessage,
      JinyaTable,
      JinyaTabContainer,
      JinyaButton,
      MonacoEditor,
    },
    methods: {
      message(name) {
        const result = this.executed.find((i) => i.name === name);
        if (!isNumber(result.data)) {
          return result.data;
        }
        return Translator.message('maintenance.database.tool.result.rows_affected', result);
      },
      isTableResult(name) {
        const result = this.executed.find((i) => i.name === name);
        return !!Array.isArray(result.data);
      },
      headers(name) {
        const result = this.executed.find((i) => i.name === name);
        const firstResult = result.data[0];
        return Object.keys(firstResult).map((key) => ({ name: key, title: key }));
      },
      rows(name) {
        const result = this.executed.find((i) => i.name === name);
        return result.data;
      },
      async executeQuery() {
        const response = await JinyaRequest.post('/api/maintenance/database/query', { query: this.query });
        this.statements = response.map((item, idx) => ({ title: item.statement, name: item.statement + idx }));
        this.executed = response.map((item, idx) => ({ data: item.result, name: item.statement + idx }));
        this.selectedStatement = this.statements[0].name;
      },
    },
    data() {
      return {
        query: '',
        statements: [],
        executed: [],
        selectedStatement: '',
      };
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-database-tool {
        display: flex;
        flex-wrap: wrap;
    }

    .jinya-database-tool__editor {
        height: 20rem;
        width: 100%;
        margin-bottom: 1rem;
    }

    .jinya-database-tool__result {
        width: 100%;
        margin-top: 1rem;
    }

    .jinya-button--execute {
        margin-left: auto;
        margin-right: 0;
    }
</style>
