<template>
  <div>
    <jinya-loader :loading="loading" v-if="loading"/>
    <template v-else>
      <h2>{{'maintenance.system.phpinfo.system.title'|jmessage}}</h2>
      <jinya-input :value="system.uname" is-static label="maintenance.system.phpinfo.system.uname"/>
      <h2>{{'maintenance.system.phpinfo.zend.title'|jmessage}}</h2>
      <jinya-input :value="zend.version" is-static label="maintenance.system.phpinfo.zend.version"/>
      <h2>{{'maintenance.system.phpinfo.apache.title'|jmessage}}</h2>
      <jinya-input :value="apache.version" is-static label="maintenance.system.phpinfo.apache.version"/>
      <h3>{{'maintenance.system.phpinfo.apache.modules'|jmessage}}</h3>
      <ul>
        <li :key="module" v-for="module in apache.modules">{{module}}</li>
      </ul>
      <h2>{{'maintenance.system.phpinfo.php.title'|jmessage}}</h2>
      <jinya-input :value="php.version" is-static label="maintenance.system.phpinfo.php.version"/>
      <h3>{{'maintenance.system.phpinfo.php.iniValues'|jmessage}}</h3>
      <JinyaDefinitionList :values="php.iniValues.map(item => ({ title: item.name, value: item.value }))"
                           horizontal/>
      <h3>{{'maintenance.system.phpinfo.php.extensions'|jmessage}}</h3>
      <template v-for="extension in php.extensions">
        <h4 :key="`${extension.name}_name`">{{extension.name}} – {{extension.version}}</h4>
        <JinyaDefinitionList :key="`${extension.name}_ini_values`"
                             :values="extension.iniValues.map(item => ({ title: item.name, value: item.value }))"
                             horizontal/>
      </template>
    </template>
  </div>
</template>

<script>
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaLoader from '@/framework/Markup/Waiting/Loader';
  import JinyaDefinitionList from '@/framework/Markup/Lists/DefinitionList';
  import JinyaInput from '@/framework/Markup/Form/Input';

  export default {
    name: 'PhpInfo',
    components: { JinyaInput, JinyaDefinitionList, JinyaLoader },
    data() {
      return {
        apache: {
          modules: [],
          version: '',
        },
        system: {
          uname: '',
        },
        php: {
          version: '',
          extensions: [],
          iniValues: [],
        },
        zend: {
          version: '',
        },
        loading: false,
      };
    },
    async mounted() {
      this.loading = true;
      const info = await JinyaRequest.get('/api/phpinfo');
      this.apache.modules = info.apache.modules;
      this.apache.version = info.apache.version;

      this.system.uname = info.system.uname;

      this.php.version = info.php.version;
      this.php.extensions = info.php.extensions;
      this.php.iniValues = info.php.iniValues;

      this.zend.version = info.zend.version;
      this.loading = false;
    },
  };
</script>

<style scoped>

</style>
