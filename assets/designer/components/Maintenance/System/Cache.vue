<template>
  <jinya-card-list nothing-found="">
    <jinya-card :header="jinyaHeader" body-modifier="jinya-card--centered" class="jinya-card--cache">
      <img :src="jinyaLogo" aria-hidden="true" class="jinya-card__image--cache">
      <jinya-card-button @click="refreshJinyaCache" icon="refresh" slot="footer"
                         tooltip="maintenance.system.cache.refresh" type="edit"/>
      <jinya-card-button @click="clearJinyaCache" icon="delete" slot="footer" tooltip="maintenance.system.cache.clear"
                         type="delete"/>
    </jinya-card>
    <jinya-card :header="symfonyHeader" body-modifier="jinya-card--centered" class="jinya-card--cache">
      <img :src="symfonyLogo" aria-hidden="true" class="jinya-card__image--cache"/>
      <jinya-card-button @click="refreshSymfonyCache" icon="refresh" slot="footer"
                         tooltip="maintenance.system.cache.refresh" type="edit"/>
      <jinya-card-button @click="clearSymfonyCache" icon="delete" slot="footer" tooltip="maintenance.system.cache.clear"
                         type="delete"/>
    </jinya-card>
    <jinya-card :header="apcuHeader" body-modifier="jinya-card--centered" class="jinya-card--cache" v-if="apcuCache">
      <img :src="phpLogo" aria-hidden="true" class="jinya-card__image--cache"/>
      <jinya-card-button @click="clearApcuCache" icon="delete" slot="footer" tooltip="maintenance.system.cache.clear"
                         type="delete"/>
    </jinya-card>
    <jinya-card :header="opacheHeader" body-modifier="jinya-card--centered" class="jinya-card--cache" v-if="opcache">
      <img :src="zendLogo" aria-hidden="true" class="jinya-card__image--cache"/>
      <jinya-card-button @click="clearOpCache" icon="delete" slot="footer" tooltip="maintenance.system.cache.clear"
                         type="delete"/>
    </jinya-card>
  </jinya-card-list>
</template>

<script>
  import JinyaCardList from '@/framework/Markup/Listing/Card/CardList';
  import JinyaCard from '@/framework/Markup/Listing/Card/Card';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import SymfonyLogo from '@/img/symfony-logo.svg';
  import JinyaLogo from '@/img/android-chrome-512x512.png';
  import PhpLogo from '@/img/php-logo.svg';
  import ZendLogo from '@/img/zend-logo.png';
  import JinyaCardButton from '@/framework/Markup/Listing/Card/CardButton';
  import Translator from '@/framework/i18n/Translator';

  export default {
    name: 'Cache',
    components: { JinyaCardButton, JinyaCard, JinyaCardList },
    methods: {
      async clearJinyaCache() {
        await JinyaRequest.delete('/api/cache/jinya');
        await this.loadData();
      },
      async clearSymfonyCache() {
        await JinyaRequest.delete('/api/cache/symfony');
        await this.loadData();
      },
      async clearApcuCache() {
        await JinyaRequest.delete('/api/cache/apcu');
        await this.loadData();
      },
      async clearOpCache() {
        await JinyaRequest.delete('/api/cache/opcache');
        await this.loadData();
      },
      async refreshJinyaCache() {
        await JinyaRequest.put('/api/cache/jinya');
        await this.loadData();
      },
      async refreshSymfonyCache() {
        await JinyaRequest.put('/api/cache/symfony');
        await this.loadData();
      },
      async loadData() {
        const data = await JinyaRequest.get('/api/cache');
        this.jinyaCache = data.jinya;
        this.apcuCache = data.apcu;
        this.opcache = data.opcache;
        this.symfonyCache = data.symfony;
      },
    },
    computed: {
      symfonyHeader() {
        if (this.symfonyCache !== null) {
          // eslint-disable-next-line max-len
          return `Symfony Cache – ${(this.symfonyCache.usedMemory / 1024 / 1024).toFixed(2)} MB`;
        }

        return 'Symfony Cache';
      },
      jinyaHeader() {
        if (this.jinyaCache !== null) {
          return `Jinya Cache – ${(this.jinyaCache.usedMemory / 1024 / 1024).toFixed(2)} MB`;
        }

        return 'Jinya Cache';
      },
      opacheHeader() {
        if (this.opcache !== null) {
          return `Zend OPCache – ${((this.opcache.usedMemory / this.opcache.freeMemory) * 100).toFixed(2)}%`;
        }

        return 'Symfony Cache';
      },
      apcuHeader() {
        if (this.apcuCache !== null) {
          return `APCu – ${this.apcuCache.usedMemory} ${Translator.message('maintenance.system.cache.entries')}`;
        }

        return 'APCu Cache';
      },
      symfonyLogo() {
        return SymfonyLogo;
      },
      jinyaLogo() {
        return JinyaLogo;
      },
      phpLogo() {
        return PhpLogo;
      },
      zendLogo() {
        return ZendLogo;
      },
    },
    data() {
      return {
        jinyaCache: null,
        apcuCache: null,
        opcache: null,
        symfonyCache: null,
      };
    },
    mounted() {
      this.loadData();
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-card__image--cache {
    object-fit: scale-down !important;
  }

  .jinya-card--cache {
    flex: 0 0 25%;
    max-width: calc(25% - 2rem);
  }
</style>

<style>
  .jinya-card--centered {
    display: flex;
    justify-content: center;
  }
</style>
