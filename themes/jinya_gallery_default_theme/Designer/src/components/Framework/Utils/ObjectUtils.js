export default {
  valueByKeypath: (target, key) => {
    return key ? key.split('.').reduce((previous, current) => {
      if (previous) {
        return previous[current];
      } else {
        return current;
      }
    }, target) || key : '';
  }
}