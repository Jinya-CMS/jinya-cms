export default {
  wait(timeout = 15 * 1000) {
    return new Promise(resolve => setTimeout(() => {
      resolve();
    }, timeout));
  }
}