import ObjectUtils from "@/framework/Utils/ObjectUtils";

const roles = {
  install(Vue) {
    Vue.directive('jinya-permission', (el, binding, vnode) => {
      console.log(vnode);
      if (binding.modifiers.includes('route')) {
        const route = ObjectUtils.valueByKeypath(binding.value);

        if (route && route.meta.me.roles.includes(route.meta.role)) {
          vnode.elm.parentElement.removeChild(vnode.elm);
        }
      } else {
        if (vnode.context.$route.meta.roles.includes(binding.expression)) {
          vnode.elm.parentElement.removeChild(vnode.elm);
        }
      }
    });
  }
};

export default roles;