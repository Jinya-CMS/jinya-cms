import ObjectUtils from "@/framework/Utils/ObjectUtils";
import Vue from "vue";
import Permissions from "@/security/Permissions";
import JinyaRequest from "@/framework/Ajax/JinyaRequest";

let me = null;

const roles = {
  async install(Vue) {
    Vue.directive('jinya-permission', async (el, binding, vnode) => {
      if (!me) {
        me = await JinyaRequest.get('/api/account').catch(() => null);
      }

      try {
        if (!(vnode.context?.$route?.meta?.me || me).roles.includes(ObjectUtils.valueByKeypath(Permissions, binding.expression))) {
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