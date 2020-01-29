import Vue from 'vue';
import Translator from '@/framework/i18n/Translator';

const i18n = {
    install(LocalVue) {
        LocalVue.directive('jinya-message', {
            isFn: true,
            acceptStatement: false,

            bind(el, binding) {
                el.innerText = Translator.message(binding.value);
            },
        });
    LocalVue.directive('jinya-validator', {
        isFn: true,
        acceptStatement: false,

        bind(el, binding) {
            el.innerText = Translator.validator(binding.value);
        },
      });

    LocalVue.filter('jmessage', (value, parameter = {}) => Translator.message(value, parameter));
    LocalVue.filter('jvalidator', (value, parameter = {}) => Translator.validator(value, parameter));
    },
};
Vue.use(i18n);

export default i18n;
