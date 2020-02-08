export default {
  getAsDataUrl(file) {
    return new Promise((resolve) => {
      const fileReader = new FileReader();
      fileReader.onload = (evt) => resolve(evt.target.result);
      fileReader.readAsDataURL(file);
    });
  },
  getAsArrayBuffer(file) {
    return new Promise((resolve) => {
      const fileReader = new FileReader();
      fileReader.onload = (evt) => resolve(evt.target.result);
      fileReader.readAsArrayBuffer(file);
    });
  },
};
