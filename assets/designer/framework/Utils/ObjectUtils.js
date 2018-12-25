/* eslint-disable no-bitwise */
import deepmerge from 'deepmerge';
import isObject from 'lodash/isObject';
import isString from 'lodash/isString';

const getHashCode = (obj) => {
  const value = JSON.stringify(obj);
  let hash = 0;

  // eslint-disable-next-line no-plusplus
  for (let i = 0; i < value.length; i++) {
    const character = value.charCodeAt(i);
    hash = ((hash << 5) - hash) + character;
    hash &= hash;
  }

  return hash;
};
const equals = (obj1, obj2) => getHashCode(obj1) === getHashCode(obj2);
const clone = obj => JSON.parse(JSON.stringify(obj));

function assign(obj, key, value) {
  if (isString(key)) {
    // eslint-disable-next-line
    key = key.split('.');
  }

  if (key.length > 1) {
    const e = key.shift();
    // eslint-disable-next-line
    assign(obj[e] = isObject(obj[e])
      ? obj[e]
      : {},
      key,
      value);
  } else {
    // eslint-disable-next-line
    obj[key[0]] = value;
  }
}

export default {
  equals,
  getHashCode,
  clone,
  setValueByKeyPath(target, key, value) {
    const data = {};
    assign(data, key, value);

    return deepmerge(target, data);
  },
  valueByKeypath(target, key, defaultValue = null) {
    const value = key ? key.split('.').reduce((previous, current) => {
      if (previous) {
        return previous[current];
      }
      return current;
    }, target) : '';

    if (defaultValue !== null && !value) {
      return defaultValue;
    }
    if (value) {
      return value;
    }
    return key;
  },
};
