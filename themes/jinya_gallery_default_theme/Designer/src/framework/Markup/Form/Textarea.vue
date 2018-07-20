<template>
    <div class="jinya-input">
        <label :for="id" class="jinya-input__label" v-jinya-message="label"></label>
        <textarea v-if="!isStatic" :disabled="!enable" :id="id" class="jinya-input__textarea" :type="type"
                  :required="required" :autocomplete="autocomplete" @keyup="$emit('change', $event.target.value)"
                  @input="$emit('input', $event.target.value)" v-model="value"></textarea>
        <span v-if="isStatic" :id="id" class="jinya-input__field jinya-input__field--no-break">{{value}}</span>
    </div>
</template>

<script>
  export default {
    name: 'jinya-textarea',
    props: {
      value: String,
      required: Boolean,
      isStatic: {
        type: Boolean,
        default() {
          return false;
        },
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        },
      },
      label: {
        type: String,
        required: true,
      },
    },
    data() {
      return {
        id: null,
      };
    },
    mounted() {
      // eslint-disable-next-line no-underscore-dangle
      this.id = this._uid;
    },
  };
</script>

<style scoped lang="scss">
    .jinya-input {
        font-size: 1rem;
        margin: 0 0 1em;
        width: 100%;

        .jinya-input__label {
            display: inline-block;
        }

        .jinya-input__textarea {
            padding: 0.5em;
            outline: none;
            width: 100%;
            display: inline-block;
            border-style: none;
            border-bottom: solid 3px $form-underline-color;
            transition: border-bottom-width 0.3s;
            font-family: $font-family;
            font-size: 90%;
            height: 10em;

            &.is--disabled {
                background: $gray-200;
            }
        }
        .jinya-input__field {
            padding: 0.5em;
            outline: none;
            width: 100%;
            display: inline-block;
            border-style: none;
            border-bottom: solid 3px $form-underline-color;
            transition: border-bottom-width 0.3s;
            font-family: $font-family;
            font-size: 90%;
            height: 10em;

            .jinya-input__field--no-break {
                word-break: keep-all;
                white-space: pre;
            }
        }
    }
</style>
