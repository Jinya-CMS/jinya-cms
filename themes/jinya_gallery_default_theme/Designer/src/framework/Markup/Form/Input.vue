<template>
    <div class="jinya-input">
        <label v-if="label" :for="id" class="jinya-input__label" v-jinya-message="label"></label>
        <!--suppress HtmlFormInputWithoutLabel, HtmlFormInputWithoutLabel -->
        <input v-if="!static" :id="id" class="jinya-input__field" :type="type" :required="required" :value="value"
               :disabled="!enable" :autocomplete="autocomplete" @keyup="keyup" :placeholder="placeholder|jmessage"
               :autofocus="autofocus" @input="$emit('input', $event.target.value)"/>
        <span v-if="static" :id="id" class="jinya-input__field">{{value}}</span>
    </div>
</template>

<script>
  export default {
    name: "jinya-input",
    props: {
      static: {
        type: Boolean,
        default() {
          return false;
        }
      },
      enable: {
        type: Boolean,
        default() {
          return true;
        }
      },
      value: String,
      placeholder: String,
      required: Boolean,
      type: {
        type: String, default() {
          return 'text';
        }
      },
      autocomplete: String,
      autofocus: Boolean,
      label: {
        type: String,
        required: false
      }
    },
    data() {
      return {
        id: null
      };
    },
    mounted() {
      this.id = this._uid;
    },
    methods: {
      keyup($event) {
        this.$emit('change', $event.target.value);
        this.$emit('keyup', $event);
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-input {
        font-size: 1rem;
        margin: 0 0 1em;
        width: 100%;

        .jinya-input__label {
            display: inline-block;
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

            &.is--disabled {
                background: $gray-200;
            }

            &:invalid {
                box-shadow: none;
                outline: none;
                border-bottom-color: $danger;
            }
        }
    }
</style>