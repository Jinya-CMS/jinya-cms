<template>
    <form @submit.prevent="submit" class="jinya-form" novalidate>
        <slot/>
        <div :style="{'padding-right': buttonBarPaddingRight}" class="jinya-form__buttons">
            <slot name="buttons">
                <jinya-button :is-disabled="!enable" :is-inverse="true" :is-secondary="true" :label="cancelLabel"
                              @click="$emit('back')" v-if="cancelLabel"/>
                <jinya-button :is-disabled="!enable" :is-inverse="true" :is-primary="true" :label="saveLabel"
                              type="submit" v-if="saveLabel"/>
            </slot>
        </div>
    </form>
</template>

<script>
  import JinyaButton from '@/framework/Markup/Button';

  export default {
    name: 'jinya-form',
    components: { JinyaButton },
    props: {
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
      novalidate: {
        type: Boolean,
        default() {
          return false;
        },
      },
      cancelLabel: {
        type: String,
      },
      saveLabel: {
        type: String,
      },
      buttonBarPaddingRight: {
        type: String,
      },
    },
    methods: {
      submit($event) {
        if (this.novalidate || $event.target.checkValidity()) {
          this.$emit('submit', $event);
        } else {
          this.$emit('invalid', $event);
        }
      },
    },
  };
</script>

<style lang="scss">
    .jinya-form {
        margin: 0;
        font-size: 1rem;
        overflow: auto;
        padding: 2em 0 0;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        width: 100%;
        flex: 0 0 100%;

        &__buttons {
            width: 100%;
            display: flex;
            justify-content: flex-end;
            align-items: start;

            .jinya-button {
                margin-right: 0.5em;
                margin-left: 0.5em;
            }
        }
    }
</style>
