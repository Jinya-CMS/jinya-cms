<template>
    <div class="jinya-input">
        <label :for="id" class="jinya-input__label" v-jinya-message="label"></label>
        <!--suppress HtmlFormInputWithoutLabel, HtmlFormInputWithoutLabel -->
        <input :id="id" class="jinya-input__field" :type="type" :required="required" :value="value"
               :autocomplete="autocomplete" @input="updateValue($event.target.value)" :autofocus="autofocus"/>
    </div>
</template>

<script>
  export default {
    name: "jinya-input",
    props: {
      'value': String,
      'required': Boolean,
      'type': {
        type: String, default() {
          return 'text';
        }
      },
      'autocomplete': String,
      'autofocus': Boolean,
      'label': {
        type: String,
        required: true
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
      updateValue(value) {
        this.$emit('input', value);
      }
    }
  }
</script>

<style scoped lang="scss">
    .jinya-input {
        font-size: 1rem;
        margin: 0 0 1em;

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
        }
    }
</style>