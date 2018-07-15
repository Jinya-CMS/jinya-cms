import ObjectUtils from "@/framework/Utils/ObjectUtils";
import Vue from "vue";
import Permissions from "@/security/Permissions";
import {getRoles} from "@/security/CurrentUser";

const roles = {
  async install(Vue) {
    Vue.directive('jinya-permission', async (el, binding, vnode) => {
      try {
        const roles = getRoles();

        if (!roles.includes(ObjectUtils.valueByKeypath(Permissions, binding.expression))) {
          vnode.elm.parentElement?.removeChild(vnode.elm);
        }
      } catch (e) {
        vnode.elm.parentElement?.removeChild(vnode.elm);
      }
    });
  }
};

Vue.use(roles);

export default roles;