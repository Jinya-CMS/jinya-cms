<template>
    <form class="jinya-form" @submit.prevent="$emit('submit', $event)">
        <slot/>
        <div class="jinya-form__buttons">
            <slot name="buttons">
                <jinya-button :is-secondary="true" :is-inverse="true" :label="cancelLabel" @click="$emit('back')"/>
                <jinya-button :is-primary="true" :is-inverse="true" :label="saveLabel" type="submit"/>
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
      cancelLabel: {
        type: String
      },
      saveLabel: {
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