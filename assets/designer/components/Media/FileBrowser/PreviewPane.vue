<template>
    <div class="jinya-file__preview-pane">
        <figure class="jinya-file__preview-figure">
            <figcaption class="jinya-file__preview-caption">{{file.name}}</figcaption>
            <img :alt="file.name" :src="file.path" class="jinya-file__preview-image"
                 v-if="getType(file.type) === 'image'"/>
            <video class="jinya-file__preview-video" controls="controls" v-else-if="getType(file.type) === 'video'">
                <source :src="file.path" :type="file.type">
            </video>
            <audio controls="controls" v-else-if="getType(file.type) === 'audio'">
                <source :src="file.path" :type="file.type">
            </audio>
            <p v-else>{{file.type}}</p>
        </figure>
        <dl class="jinya-file__preview-data">
            <dt>{{'media.files.details.created_by'|jmessage}}</dt>
            <dd>
                {{file.created.by.artistName}}
                <a :href="`mailto:${file.created.by.email}`">{{file.created.by.email}}</a>
            </dd>
            <dt>{{'media.files.details.created_at'|jmessage}}</dt>
            <dd>{{convertTime(file.created.at)}}</dd>
            <dt>{{'media.files.details.updated_by'|jmessage}}</dt>
            <dd>
                {{file.updated.by.artistName}}
                <a :href="`mailto:${file.updated.by.email}`">{{file.updated.by.email}}</a>
            </dd>
            <dt>{{'media.files.details.updated_at'|jmessage}}</dt>
            <dd>{{convertTime(file.updated.at)}}</dd>
        </dl>
    </div>
</template>

<script>
  import startsWith from 'lodash/startsWith';
  import moment from 'moment';

  export default {
    name: 'PreviewPane',
    props: {
      file: {
        type: Object,
        required: true,
      },
    },
    methods: {
      convertTime(item) {
        const date = moment(item);
        return date.local().format('L');
      },
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
    .jinya-file__preview-pane {
        flex: 1 1 20%;
        min-width: 20%;
        transition: all 0.3s;
        padding: 1rem;
        box-sizing: border-box;
        position: sticky;
        top: 2rem;
        height: 100%;
        border-radius: 10px;
        background: $primary-lightest;
    }

    .jinya-file__preview-figure {
        margin: 0;
    }

    .jinya-file__preview-image {
        width: 100%;
        object-fit: scale-down;
    }

    .jinya-file__preview-video {
        width: 100%;
    }

    .jinya-file__preview-caption {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .jinya-file__preview-data {
        dt {
            padding: 0;
            font-weight: bold;
        }

        dd {
            padding: 0;
            margin: 0 0 0.5rem;
        }
    }
</style>
