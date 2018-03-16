import Translator from "@/components/Framework/i18n/Translator";
import Vue from 'vue';

const i18n = {
  install(Vue) {
    Vue.directive('jinya-message', {
      isFn: true,
      acceptStatement: false,

      bind(el, binding) {
        const key = binding.value;
        const message = Translator.message(key);

        if (binding.modifiers && binding.modifiers.length > 0) {
          for (let modifier in binding.modifiers) {
            if (el.hasOwnProperty(modifier)) {
              el.setAttribute(modifier, message);
            }
          }
        } else {
          el.innerText = message;
        }
      }
    });
    Vue.directive('jinya-validator', {
      isFn: true,
      acceptStatement: false,

      bind(el, binding) {
        const key = binding.value;
        const validator = Translator.validator(key);

        if (binding.modifiers && binding.modifiers.length > 0) {
          for (let modifier in binding.modifiers) {
            if (el.hasOwnProperty(modifier)) {
              el.setAttribute(modifier, validator);
            }
          }
        } else {
          el.innerText = validator;
        }
      }
    });

    Vue.filter('jmessage', (value) => {
      return Translator.message(value);
    });
    Vue.filter('jvalidator', (value) => {
      return Translator.validator(value);
    });
  }
};
Vue.use(i18n);

export default i18n;