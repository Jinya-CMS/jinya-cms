<template>
    <div :class="{'is--invalid': invalid}" :data-validation-message="validationMessage" class="jinya-input">
        <label :for="id" class="jinya-input__label" v-if="label">{{label|jmessage || label}}</label>
        <input :autocomplete="autocomplete" :autofocus="autofocus" :disabled="!enable" :id="id"
               :placeholder="placeholder|jmessage" :required="required"
               :type="type" :value="value" @change="change" @input="input"
               @invalid="onInvalid" @keyup="keyup" class="jinya-input__field" v-if="!isStatic"/>
        <span :id="id" class="jinya-input__field" v-if="isStatic">{{value}}</span>
    </div>
</template>

<script>
  export default {
    name: 'jinya-input',
    props: {
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
      validationMessage: String,
      value: String,
      placeholder: String,
      required: Boolean,
      type: {
        type: String,
        default() {
          return 'text';
        },
      },
      autocomplete: String,
      autofocus: Boolean,
      label: {
        type: String,
        required: false,
      },
    },
    data() {
      return {
        id: null,
        invalid: false,
      };
    },
    mounted() {
      // eslint-disable-next-line no-underscore-dangle
      this.id = this._uid;
    },
    methods: {
      onInvalid($event) {
        this.invalid = true;
        this.$emit('invalid', $event.target.validity);
      },
      change($event) {
        this.$emit('change', $event.target.value);
        if ($event.target.checkValidity()) {
          this.invalid = false;
          this.$emit('valid');
        } else {
          this.invalid = true;
          this.$emit('invalid', $event.target.validity);
        }
      },
      keyup($event) {
        this.$emit('keyup', $event);
        this.change($event);
      },
      input($event) {
        this.$emit('input', $event.target.value);
        this.change($event);
      },
    },
  };
</script>

<style lang="scss" scoped>
    .jinya-input {
        font-size: 1rem;
        width: 100%;
        @include validation-message(3.4rem);

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
            position: relative;

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
