import deepmerge from 'deepmerge';

export default {
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