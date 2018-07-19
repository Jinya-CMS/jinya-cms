export default {
  async asyncForeach(array, callback) {
    for (const item of array) {
      await callback(item, array.indexOf(item));
    }
  },
};
