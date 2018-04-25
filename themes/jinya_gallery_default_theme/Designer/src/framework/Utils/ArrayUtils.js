export default {
  async asyncForeach(array, callback) {
    for (let item of array) {
      await callback(item, array.indexOf(item));
    }
  }
}