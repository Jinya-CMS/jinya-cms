<template>
    <jinya-card-list class="jinya-card__list--files" nothing-found="media.files.list.nothing_found">
        <jinya-card :header="file.name" :key="file.id" v-for="file in files">
            <img :alt="file.name" :src="file.path" class="jinya-card__body--file-picture"
                 v-if="getType(file.type) === 'image'"/>
            <video controls="controls" v-else-if="getType(file.type) === 'video'">
                <source :src="file.path" :type="file.type">
            </video>
            <audio controls="controls" v-else-if="getType(file.type) === 'audio'">
                <source :src="file.path" :type="file.type">
            </audio>
            <p v-else>{{file.type}}</p>
            <jinya-card-button :title="'media.files.list.details'|jmessage" @click="$emit('fileChanged', file)"
                               icon="eye-outline" slot="footer" type="details"/>
            <jinya-card-button :title="'media.files.list.edit'|jmessage" @click="$emit('editFile', file)" icon="pencil"
                               slot="footer" type="edit"/>
            <jinya-card-button :title="'media.files.list.delete'|jmessage" @click="$emit('deleteFile', file)"
                               icon="file-remove" slot="footer" type="delete"/>
        </jinya-card>
    </jinya-card-list>
</template>

<script>
  import startsWith from 'lodash/startsWith';
  import JinyaCard from '@/framework/Markup/Listing/Card/Card';
  import JinyaCardList from '@/framework/Markup/Listing/Card/CardList';
  import JinyaCardButton from '@/framework/Markup/Listing/Card/CardButton';

  export default {
    name: 'FileView',
    components: { JinyaCardButton, JinyaCardList, JinyaCard },
    props: {
      files: {
        type: Array,
        required: true,
      },
    },
    methods: {
      getType(type) {
        if (startsWith(type, 'image')) {
          return 'image';
        }
        if (startsWith(type, 'video')) {
          return 'video';
        }
        if (startsWith(type, 'audio')) {
          return 'audio';
        }

        return type;
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-card__body--file-picture {
        width: 100%;
        height: 15em;
        object-fit: cover;
        transition: all 0.3s;
        justify-self: center;
        margin-right: auto;
        margin-left: auto;
    }

    .jinya-card__list--files {
        flex: 1 1 80%;
        min-width: 80%;
        transition: all 0.3s;
        margin-top: -1%;
    }
</style>
