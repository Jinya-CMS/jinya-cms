/**
 * @param file File
 * @param start number
 * @param end number
 * @returns {Promise<string>}
 */
export async function readDataUrl(file, start = 0, end = -1) {
  return new Promise((resolve, reject) => {
    if (!file) {
      throw new Error('File must be not null');
    }

    if (start > end && end !== -1) {
      throw new Error('Start can not be before end');
    }

    const fileReader = new FileReader();
    fileReader.addEventListener('load', (event) => {
      resolve(fileReader.result);
    });
    fileReader.readAsDataURL(file);
  });
}
