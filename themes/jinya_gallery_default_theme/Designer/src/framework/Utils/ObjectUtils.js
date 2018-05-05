import deepmerge from 'deepmerge';

const getHashCode = (obj) => {
  const value = JSON.stringify(obj);
  let hash = 0;

  for (let i = 0; i < value.length; i++) {
    const character = value.charCodeAt(i);
    hash = ((hash << 5) - hash) + character;
    hash = hash & hash;
  }

  return hash;
};
const equals = (obj1, obj2) => {
  return getHashCode(obj1) === getHashCode(obj2);
};

export default {
  equals,
  getHashCode,
  setValueByKeyPath(target, key, value) {
    const data = {};
    key.split('.').reduce((previousValue, currentValue, currentIndex, array) => {
      let newValue = {};
      if (currentIndex + 1 === array.length) {
        newValue = value;
      }

      previousValue[currentValue] = newValue;
      return previousValue[currentValue];
    }, data);

    return deepmerge(target, data);
  },
  valueByKeypath(target, key, defaultValue = null) {
    const value = key ? key.split('.').reduce((previous, current) => {
      if (previous) {
        return previous[current];
      } else {
        return current;
      }
    }, target) : '';

    if (defaultValue !== null && !value) {
      return defaultValue;
    } else if (value) {
      return value;
    } else {
      return key;
    }
  }
}