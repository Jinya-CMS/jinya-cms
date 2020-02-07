import ObjectUtils from '../Utils/ObjectUtils';

const replaceTokens = (parameter) => (
  accumulator,
  currentValue,
) => accumulator.replace(new RegExp(`%${currentValue}%`, 'g'), parameter[currentValue]);

export default {
    hasMessage(key) {
        return ObjectUtils.valueByKeypath(window.messages, key, false);
    },
    hasValidator(key) {
        return ObjectUtils.valueByKeypath(window.validators, key, false);
    },
    message(key, parameter = {}) {
        return Object
        .keys(parameter)
        .reduce(replaceTokens(parameter), ObjectUtils.valueByKeypath(window.messages, key)) || key;
    },
    validator(key, parameter = {}) {
        return Object
        .keys(parameter)
        .reduce(replaceTokens(parameter), ObjectUtils.valueByKeypath(window.validators, key)) || key;
    },
};
