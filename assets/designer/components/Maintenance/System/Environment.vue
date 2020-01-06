<template>
  <jinya-table :headers="headers" :rows="environmentVariables"/>
</template>

<script>
  import JinyaTable from '@/framework/Markup/Table/Table';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import Translator from '@/framework/i18n/Translator';

  export default {
    name: 'Environment',
    components: { JinyaTable },
    computed: {
      headers() {
        return [
          { name: 'key', title: Translator.message('maintenance.system.environment.value') },
          { name: 'value', title: Translator.message('maintenance.system.environment.value') },
        ];
      },
    },
    data() {
      return { environmentVariables: [] };
    },
    async mounted() {
      this.environmentVariables = await JinyaRequest.get('/api/environment');
    },
  };
</script>

<style scoped>

</style>
