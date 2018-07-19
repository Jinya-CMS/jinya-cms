<template>
    <div class="jinya-updater">
        <jinya-message :message="updateMessage|jmessage({version: jinyaVersion})" :state="updateState"/>
        <jinya-message :message="'maintenance.system.updates.disclaimer'|jmessage" state="warning"
                       v-if="updateState === 'info'">
            <jinya-message-action-bar>
                <jinya-button label="maintenance.system.updates.back" @click="back" :is-secondary="true"></jinya-button>
                <jinya-button label="maintenance.system.updates.start" @click="startUpdate"
                              :is-primary="true"></jinya-button>
            </jinya-message-action-bar>
        </jinya-message>
    </div>
</template>

<script>
  import JinyaButton from '@/framework/Markup/Button';
  import Routes from '@/router/Routes';
  import JinyaRequest from '@/framework/Ajax/JinyaRequest';
  import JinyaMessageActionBar from '@/framework/Markup/Validation/MessageActionBar';
  import JinyaMessage from '@/framework/Markup/Validation/Message';

  export default {
    name: 'Update',
    components: {
      JinyaMessage,
      JinyaMessageActionBar,
      JinyaButton,
    },
    async mounted() {
      this.jinyaVersion = await JinyaRequest.get('/api/maintenance/version');
    },
    data() {
      return {
        jinyaVersion: '',
        updateMessage: 'maintenance.system.updates.message',
        updateState: 'info',
      };
    },
    methods: {
      back() {
        this.$router.push(Routes.Home.StartPage);
      },
      async startUpdate() {
        this.updateMessage = 'maintenance.system.updates.started';
        this.updateState = 'loading';
        window.location = await JinyaRequest.post('/api/maintenance/update');
      },
    },
  };
</script>

<style scoped lang="scss">
    .jinya-updater {
        padding-top: 1rem;

        .jinya-message {
            margin-bottom: 1rem;
        }
    }
</style>
