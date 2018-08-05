<template>
  <form class="jinya-form" novalidate @submit.prevent="submit">
    <slot/>
    <div class="jinya-form__buttons" :style="{'padding-right': buttonBarPaddingRight}">
      <slot name="buttons">
        <jinya-button :is-disabled="!enable" :is-secondary="true" :is-inverse="true" :label="cancelLabel"
                      @click="$emit('back')" v-if="cancelLabel"/>
        <jinya-button :is-disabled="!enable" :is-primary="true" :is-inverse="true" :label="saveLabel"
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
        if ($event.target.checkValidity()) {
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

    &__buttons {
      width: 100%;
      display: flex;
      justify-content: flex-end;

      .jinya-button {
        margin-right: 0.5em;
        margin-left: 0.5em;

        &:first-child {
          margin-left: 0;
        }

        &:last-child {
          margin-right: 0;
        }
      }
    }
  }
</style>
