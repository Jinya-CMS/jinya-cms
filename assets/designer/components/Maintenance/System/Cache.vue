<template>
  <jinya-card-list nothing-found="">
    <jinya-card :header="jinyaHeader" body-modifier="jinya-card--centered">
      <img :src="jinyaLogo" aria-hidden="true" class="jinya-card__image--cache">
    </jinya-card>
    <jinya-card :header="symfonyHeader" body-modifier="jinya-card--centered">
      <img :src="symfonyLogo" aria-hidden="true" class="jinya-card__image--cache"/>
    </jinya-card>
    <jinya-card :header="apcuHeader" body-modifier="jinya-card--centered" v-if="apcuCache">
      <img :src="phpLogo" aria-hidden="true" class="jinya-card__image--cache"/>
    </jinya-card>
    <jinya-card :header="opacheHeader" body-modifier="jinya-card--centered" v-if="opcache">
      <img :src="zendLogo" aria-hidden="true" class="jinya-card__image--cache"/>
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

  export default {
    name: 'Cache',
    components: { JinyaCard, JinyaCardList },
    computed: {
      symfonyHeader() {
        if (this.symfonyCache !== null) {
          return `Symfony Cache – ${this.symfonyCache.usedMemory} Byte`;
        }

        return 'Symfony Cache';
      },
      jinyaHeader() {
        if (this.jinyaCache !== null) {
          return `Jinya Cache – ${this.jinyaCache.usedMemory} Byte`;
        }

        return 'Jinya Cache';
      },
      opacheHeader() {
        if (this.opcache !== null) {
          return `Zend OPCache Cache – ${(this.opcache.usedMemory / this.opcache.freeMemory) * 100}%`;
        }

        return 'Symfony Cache';
      },
      apcuHeader() {
        if (this.apcuCache !== null) {
          return `APCu Cache – ${this.apcuCache.usedMemory} Entries`;
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
    async mounted() {
      const data = await JinyaRequest.get('/api/cache');
      this.jinyaCache = data.jinya;
      this.apcuCache = data.apcu;
      this.opcache = data.opcache;
      this.symfonyCache = data.symfony;
    },
  };
</script>

<style lang="scss" scoped>
  .jinya-card__image--cache {
    object-fit: scale-down !important;
  }
</style>

<style>
  .jinya-card--centered {
    display: flex;
    justify-content: center;
  }
</style>
