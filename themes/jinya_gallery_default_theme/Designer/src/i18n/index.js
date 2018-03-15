import Translator from "@/components/Framework/i18n/Translator";

export default {
  install(Vue) {
    Vue.directive('jinya-message', {
      isFn: true,
      acceptStatement: false,

      bind: function (el, binding) {
        const key = binding.value;
        const message = Translator.message(key);
        console.log(`${key} => ${message}`);

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

      bind: function (el, binding) {
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
  }
}