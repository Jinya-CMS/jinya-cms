<template>
    <div class="jinya-choice">
        <label :for="id" class="jinya-choice__label">{{label|jmessage}}</label>
        <span :id="id" v-if="static" class="jinya-choice__field">{{selectionText}}</span>
        <!--suppress HtmlFormInputWithoutLabel -->
        <select @change="$emit('selected', {value:$event.target.value, text: $event.target.innerText})" :id="id"
                v-if="showSelect" class="jinya-choice__field" :disabled="!enable" :multiple="multiple"
                :class="{'is--disabled': !enable}">
            <template v-for="choice in choices">
                <option :selected="isSelected(choice)" :value="choice.value">
                    {{choice.text}}
                </option>
            </template>
        </select>
        <template v-for="choice in choices">
            <label :id="id" v-if="showCheckboxes" class="jinya-choice__checkbox">
                <input @change="$emit('selected', {value:$event.target.value, text: $event.target.innerText})"
                       :name="label" type="checkbox" :checked="isSelected(choice)" :disabled="!enable"
                       :value="choice.value">
                {{choice.text}}
            </label>
            <label :id="id" v-if="showRadioButtons" class="jinya-choice__radio">
                <input @change="$emit('selected', {value:$event.target.value, text: $event.target.innerText})"
                       :name="label" type="radio" :checked="isSelected(choice)" :disabled="!enable"
                       :value="choice.value">
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
      static: {
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
        return this.enforceSelect || (!this.static && this.choices.length > 5);
      },
      showCheckboxes() {
        return !this.enforceSelect && !this.static && (this.multiple && this.choices.length <= 5);
      },
      showRadioButtons() {
        return !this.enforceSelect && !this.static && (!this.multiple && this.choices.length <= 5);
      },
      selectionText() {
        if (this.selected instanceof Array) {
          return this.selected.map(selection => selection.text).join(', ');
        }
        return this.selected.text;
      },
    },
    methods: {
      isSelected(value) {
        if (this.selected instanceof Array) {
          return this.selected.filter(item => value.value.toString() === item.value.toString()).length > 0;
        }
        if (this.selected instanceof Object) {
          return this.selected.value.toString() === value.value.toString();
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
      this.id = this._uid;
    },
  };
</script>

<style scoped lang="scss">
    .jinya-choice {
        font-size: 1rem;
        margin: 0 0 1em;

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
