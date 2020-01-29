<template>
    <div>
        <h1>{{'maintenance.system.changelog.currentVersion'|jmessage({version})}}</h1>
        <vue-markdown :source="markdown"/>
    </div>
</template>

<script>
  import vueMarkdown from 'vue-markdown';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';

  export default {
    components: {
      vueMarkdown,
    },
    name: 'Version',
    data() {
      return {
        markdown: '',
        version: '',
      };
    },
    async mounted() {
      const data = await JinyaRequest.get('/api/changelog');
      this.markdown = data.changelog;
      this.version = data.version;
    },
  };
</script>

<style scoped>

</style>
