import set from 'lodash/set';
import get from 'lodash/get';
import cloneDeep from 'lodash/cloneDeep';
import isEqual from 'lodash/isEqual';

export default {
  equals(a, b) {
    return isEqual(a, b);
  },
  clone(object) {
    return cloneDeep(object);
  },
  setValueByKeyPath(target, key, value) {
    return set(target, key, value);
  },
  valueByKeypath(target, key, defaultValue = null) {
    return get(target, key, defaultValue);
  },
};
