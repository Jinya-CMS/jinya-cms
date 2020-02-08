<template>
    <div class="jinya-choice">
        <label :for="id" class="jinya-choice__label">{{label|jmessage}}</label>
        <span :id="id" class="jinya-choice__field" v-if="isStatic">{{selectionText}}</span>
        <!--suppress HtmlFormInputWithoutLabel -->
        <select :class="{'is--disabled': !enable}" :disabled="!enable"
                :id="id" :multiple="multiple"
                @change="$emit('selected', {value: $event.target.value, text: $event.target.innerText})"
                class="jinya-choice__field"
                v-if="showSelect">
            <template v-for="choice in choices">
                <option :key="choice.value" :selected="isSelected(choice)" :value="choice.value">
                    {{choice.text}}
                </option>
            </template>
        </select>
        <template v-for="choice in choices">
            <label :id="id" :key="choice.value" class="jinya-choice__checkbox" v-if="showCheckboxes">
                <input :checked="isSelected(choice)"
                       :disabled="!enable" :name="label" :value="choice.value"
                       @change="$emit('selected', {value:$event.target.value, text: $event.target.innerText})"
                       type="checkbox">
                {{choice.text}}
            </label>
            <label :id="id" :key="choice.value" class="jinya-choice__radio" v-if="showRadioButtons">
                <input :checked="isSelected(choice)"
                       :disabled="!enable" :name="label" :value="choice.value"
                       @change="$emit('selected', {value:$event.target.value, text: $event.target.innerText})"
                       type="radio">
                {{choice.text}}
            </label>
        </template>
    </div>
</template>

<script>
  export default {
    name: 'jinya-choice',
    props: {
      enforceSelect: {
        type: Boolean,
        default() {
          return false;
        },
      },
      isStatic: {
        type: Boolean,
        default() {
          return false;
        },
      },
      multiple: {
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
      selected: {
        default() {
          return {
            text: '',
            value: '',
          };
        },
      },
      choices: {
        type: Array,
        required: true,
      },
    },
    computed: {
      showSelect() {
        return (this.enforceSelect && !this.isStatic) || (!this.isStatic && this.choices.length > 5);
      },
      showCheckboxes() {
        return !this.enforceSelect && !this.isStatic && this.multiple && this.choices.length <= 5;
      },
      showRadioButtons() {
        return !this.enforceSelect && !this.isStatic && !this.multiple && this.choices.length <= 5;
      },
      selectionText() {
        if (this.selected instanceof Array) {
          return this.selected.map((selection) => selection.text).join(', ');
        }
        return this.selected.text;
      },
    },
    methods: {
      isSelected(value) {
        if (this.selected instanceof Array) {
          return this.selected.filter((item) => value.value.toString() === item.value.toString()).length > 0;
        }
        if (this.selected instanceof Object) {
          return this.selected.value.toString() === value.value.toString();
        }
        if (typeof this.selected === 'string' || this.selected instanceof String) {
          return this.selected === value.value.toString();
        }
        return false;
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

<style lang="scss" scoped>
    .jinya-choice {
        font-size: 1rem;
        margin: 0 0 1em;
        width: 100%;

        .jinya-choice__label {
            display: block;
        }

        .jinya-choice__list {
            padding: 0;
            margin: 0;
            list-style: none;

            li {
                border-bottom: solid 1px $form-underline-color;

                &:last-child {
                    border-bottom-width: 3px;
                }
            }
        }

        .jinya-choice__field {
            padding: 0.5em;
            outline: none;
            width: 100%;
            display: inline-block;
            border-style: none;
            border-bottom: solid 3px $form-underline-color;
            font-family: $font-family;
            font-size: 90%;

            &.is--disabled {
                background: $gray-200;
            }
        }

        .jinya-choice__checkbox,
        .jinya-choice__radio {
            padding: 0.5em;
            outline: none;
            width: 100%;
            font-family: $font-family;
            font-size: 90%;
            display: flex;

            > input {
                margin-right: 1em;
            }
        }
    }
</style>
