<template>
    <jinya-card-list>
        <jinya-card :header="artwork.name" v-for="artwork in artworks">
            <img class="jinya-art-picture" :src="artwork.picture"/>
            <button class="jinya-art-button jinya-art-button--details" slot="footer"><span
                    class="mdi mdi-monitor"></span></button>
            <button class="jinya-art-button jinya-art-button--edit" slot="footer"><span class="mdi mdi-pencil"></span>
            </button>
            <button class="jinya-art-button jinya-art-button--delete" slot="footer"><span class="mdi mdi-delete"></span>
            </button>
        </jinya-card>
    </jinya-card-list>
</template>

<script>
  import ArtworksBase from './ArtworksBase';
  import JinyaRequest from "../../../Framework/Ajax/JinyaRequest";
  import JinyaCardList from "../../../Framework/Markup/Display/CardList";
  import JinyaCard from "../../../Framework/Markup/Display/Card";

  export default {
    components: {
      JinyaCard,
      JinyaCardList
    },
    extends: ArtworksBase,
    name: "overview",
    beforeCreate() {
      JinyaRequest.get('/api/artwork').then(value => {
        this.artworks = value.items;
        this.control = value.control;
        this.count = value.count;
        this.offset = value.offset;
      });
    },
    data() {
      return {
        artworks: [],
        control: {next: false, previous: false},
        count: 0,
        offset: 0
      };
    }
  }
</script>

<style scoped lang="scss">
    .jinya-art-picture {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s;
        position: relative;
        z-index: 10;

        &:hover {
            object-fit: scale-down;
        }
    }

    @include card-footer-button('jinya-art-button');
</style>