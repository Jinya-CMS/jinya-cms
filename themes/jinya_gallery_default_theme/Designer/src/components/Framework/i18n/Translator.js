import ObjectUtils from "../Utils/ObjectUtils";

export default {
  message(key, parameter = {}) {
    return Object
      .keys(parameter)
      .reduce((accumulator, currentValue) =>
          accumulator.replace(new RegExp(`%${currentValue}%`), parameter[currentValue]),
        ObjectUtils.valueByKeypath(window.messages, key)
      );
  },
  validator(key, parameter = {}) {
    return Object
      .keys(parameter)
      .reduce((accumulator, currentValue) =>
          accumulator.replace(new RegExp(`%${currentValue}%`), parameter[currentValue]),
        ObjectUtils.valueByKeypath(window.validators, key)
      );
  }
}