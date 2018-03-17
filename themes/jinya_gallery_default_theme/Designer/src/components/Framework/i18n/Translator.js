import ObjectUtils from "../Utils/ObjectUtils";

export default {
  message(key) {
    return ObjectUtils.valueByKeypath(window.messages, key);
  },
  validator(key) {
    return ObjectUtils.valueByKeypath(window.validators, key);
  }
}