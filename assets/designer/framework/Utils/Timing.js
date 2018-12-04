export default {
  wait(timeout = 5 * 1000) {
    return new Promise(resolve => setTimeout(() => {
      resolve();
    }, timeout));
  },
};
