<template>
    <form class="jinya-form" @submit.prevent="$emit('submit', $event)">
        <slot/>
        <div class="jinya-form__buttons" :style="{'padding-right': buttonBarPaddingRight}">
            <slot name="buttons">
                <jinya-button :is-disabled="!enable" :is-secondary="true" :is-inverse="true" :label="cancelLabel"
                              @click="$emit('back')"/>
                <jinya-button :is-disabled="!enable" :is-primary="true" :is-inverse="true" :label="saveLabel"
                              type="submit"/>
            </slot>
        </div>
    </form>
</template>

<script>
  import JinyaButton from "../Button";

  export default {
    components: {JinyaButton},
    name: "jinya-form",
    props: {
      enable: {
        type: Boolean,
        default() {
          return true;
        }
      },
      cancelLabel: {
        type: String
      },
      saveLabel: {
        type: String
      },
      buttonBarPaddingRight: {
        type: String
      }
    }
  }
</script>

<style lang="scss">
    .jinya-form {
        margin: 0;
        padding: 0;
        font-size: 1rem;
        overflow: auto;
        padding-top: 2em;
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