export default {
  valueByKeypath: (target, key) => {
    return key.split('.').reduce((previous, current) => {
      return previous[current];
    }, target) || key;
  }
}