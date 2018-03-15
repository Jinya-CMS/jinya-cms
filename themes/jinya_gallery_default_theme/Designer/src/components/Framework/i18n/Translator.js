const valueByKeypath = function (target, key) {
  return key.split('.').reduce((previous, current) => {
    return previous[current];
  }, target) || key;
};

export default {
  message(key) {
    return valueByKeypath(window.messages, key);
  },
  validator(key) {
    return valueByKeypath(window.validators, key);
  }
}