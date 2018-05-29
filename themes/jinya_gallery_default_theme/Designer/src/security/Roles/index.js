import ObjectUtils from "@/framework/Utils/ObjectUtils";
import Vue from "vue";
import Routes from "@/router/Routes";
import Permissions from "@/security/Permissions";

const roles = {
  install(Vue) {
    Vue.directive('jinya-permission', (el, binding, vnode) => {
      if (binding.modifiers.route) {
        const route = ObjectUtils.valueByKeypath(Routes, binding.value);
        const permission = Permissions[route.name];
        const meta = vnode.context.$route.meta;

        if (!meta.me.roles.includes(permission?.role)) {
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

Vue.use(roles);

export default roles;