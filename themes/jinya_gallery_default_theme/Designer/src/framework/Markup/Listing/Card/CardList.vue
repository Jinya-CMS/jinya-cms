<template>
    <div class="jinya-card-list">
        <slot>
            <jinya-message state="info" :message="nothingFound"/>
        </slot>
    </div>
</template>

<script>
  import JinyaMessage from '@/framework/Markup/Validation/Message';

  export default {
    components: { JinyaMessage },
    name: 'jinya-card-list',
    props: {
      nothingFound: {
        type: String,
        required: true,
      },
    },
  };
</script>

<style scoped lang="scss">
    .jinya-card-list {
        display: flex;
        flex-flow: row wrap;
        width: 100%;
        overflow: hidden;
        padding: 1% 0;

        .jinya-message {
            margin: 1em;
        }
    }
</style>
