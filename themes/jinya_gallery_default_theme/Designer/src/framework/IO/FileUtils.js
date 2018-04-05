export default {
  async getAsDataUrl(file) {
    return await new Promise(resolve => {
      const fileReader = new FileReader();
      fileReader.onload = evt => resolve(evt.target.result);
      fileReader.readAsDataURL(file);
    });
  },
  async getAsArrayBuffer(file) {
    return await new Promise(resolve => {
      const fileReader = new FileReader();
      fileReader.onload = evt => resolve(evt.target.result);
      fileReader.readAsArrayBuffer(file);
    });
  }
}