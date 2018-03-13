import Translator from "@/components/Framework/i18n/Translator";

export default {
  install(Vue) {
    Vue.directive('jinya-message', {
      isFn: true,
      acceptStatement: false,

      bind: function (el, binding) {
        const key = binding.value;
        el.innerText = Translator.message(key);
      }
    });
    Vue.directive('jinya-validator', {
      isFn: true,
      acceptStatement: false,

      bind: function (el, binding) {
        const key = binding.value;
        el.innerText = Translator.validator(key);
      }
    });
  }
}