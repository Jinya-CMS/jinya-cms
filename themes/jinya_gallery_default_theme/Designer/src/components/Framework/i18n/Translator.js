import ObjectUtils from "../Utils/ObjectUtils";

const replaceTokens = (parameter) => {
  return (accumulator, currentValue) => {
    return accumulator.replace(new RegExp(`%${currentValue}%`, 'g'), parameter[currentValue]);
  };
};

export default {
  message(key, parameter = {}) {
    return Object
      .keys(parameter)
      .reduce(replaceTokens(parameter), ObjectUtils.valueByKeypath(window.messages, key));
  },
  validator(key, parameter = {}) {
    return Object
      .keys(parameter)
      .reduce(replaceTokens(parameter), ObjectUtils.valueByKeypath(window.validators, key));
  }
}