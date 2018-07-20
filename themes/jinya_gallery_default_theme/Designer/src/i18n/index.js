import Translator from '@/framework/i18n/Translator';
import Vue from 'vue';

const i18n = {
  install(LocalVue) {
    LocalVue.directive('jinya-message', {
      isFn: true,
      acceptStatement: false,

      bind(el, binding) {
        // eslint-disable-next-line no-param-reassign
        el.innerText = Translator.message(binding.value);
      },
    });
    LocalVue.directive('jinya-validator', {
      isFn: true,
      acceptStatement: false,

      bind(el, binding) {
        // eslint-disable-next-line no-param-reassign
        el.innerText = Translator.validator(binding.value);
      },
    });

    LocalVue.filter('jmessage', (value, parameter = {}) => Translator.message(value, parameter));
    LocalVue.filter('jvalidator', (value, parameter = {}) => Translator.validator(value, parameter));
  },
};
Vue.use(i18n);

export default i18n;
