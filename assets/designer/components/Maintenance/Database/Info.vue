<template>
    <jinya-loader v-if="loading"/>
    <div v-else>
        <jinya-tab-container :items="tabs" :selected-item="selectedTab" @select="selectedTab = $event">
            <jinya-tab :is-selected="selectedTab === 'serverinfo'">
                <jinya-input :value="serverType" is-static label="maintenance.database.info.server.type"/>
                <jinya-input :value="serverVersion" is-static label="maintenance.database.info.server.version"/>
            </jinya-tab>
            <jinya-tab :is-selected="selectedTab === 'local_variables'">
                <jinya-definition-list
                    :values="localVariables.map(item => ({ title: item.Variable_name, value: item.Value }))"
                    horizontal/>
            </jinya-tab>
            <jinya-tab :is-selected="selectedTab === 'global_variables'">
                <jinya-definition-list
                    :values="globalVariables.map(item => ({ title: item.Variable_name, value: item.Value }))"
                    horizontal/>
            </jinya-tab>
        </jinya-tab-container>
    </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaTabContainer from '@/framework/Markup/Tab/TabContainer';
  import JinyaTab from '@/framework/Markup/Tab/Tab';
  import JinyaInput from '@/framework/Markup/Form/Input';
  import JinyaDefinitionList from '@/framework/Markup/Lists/DefinitionList';
  import Translator from '@/framework/i18n/Translator';

  export default {
    name: 'Info',
    components: {
      JinyaDefinitionList,
      JinyaInput,
      JinyaTab,
      JinyaTabContainer,
      JinyaLoader,
    },
    computed: {
      tabs() {
        return [
          {
            name: 'serverinfo',
            title: Translator.message('maintenance.database.info.tabs.server'),
          },
          {
            name: 'local_variables',
            title: Translator.message('maintenance.database.info.tabs.local_variables'),
          },
          {
            name: 'global_variables',
            title: Translator.message('maintenance.database.info.tabs.global_variables'),
          },
        ];
      },
    },
    data() {
      return {
        globalVariables: [],
        localVariables: [],
        serverType: '',
        serverVersion: '',
        loading: true,
        selectedTab: 'serverinfo',
      };
    },
    async mounted() {
      const info = await JinyaRequest.get('/api/maintenance/database');
      this.localVariables = info.variables.local;
      this.globalVariables = info.variables.global;
      this.serverType = info.server.type;
      this.serverVersion = info.server.version;
      this.loading = false;
    },
  };
</script>

<style scoped>

</style>
