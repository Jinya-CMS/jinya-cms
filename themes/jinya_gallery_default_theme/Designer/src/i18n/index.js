import Translator from "@/components/Framework/i18n/Translator";
import Vue from 'vue';

const i18n = {
  install(Vue) {
    Vue.directive('jinya-message', {
      isFn: true,
      acceptStatement: false,

      bind(el, binding) {
        el.innerText = Translator.message(binding.value);
      }
    });
    Vue.directive('jinya-validator', {
      isFn: true,
      acceptStatement: false,

      bind(el, binding) {
        el.innerText = Translator.validator(binding.value);
      }
    });

    Vue.filter('jmessage', (value, parameter = {}) => {
      return Translator.message(value, parameter);
    });
    Vue.filter('jvalidator', (value, parameter = {}) => {
      return Translator.validator(value, parameter);
    });
  }
};
Vue.use(i18n);

export default i18n;