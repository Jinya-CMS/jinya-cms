/**
 * Reads the given blob into a data url
 * @param blob {Blob}
 * @param start {number}
 * @param end {number}
 * @returns {Promise<string>}
 */
// eslint-disable-next-line import/prefer-default-export
export async function dataUrlReader({ file: blob, start = 0, end = -1 }) {
  return new Promise((resolve) => {
    if (!blob) {
      throw new Error('Blob must be not null');
    }

    if (start > end && end !== -1) {
      throw new Error('Start can not be greater than end');
    }

    const fileReader = new FileReader();
    fileReader.addEventListener('load', () => {
      resolve(fileReader.result);
    });
    fileReader.readAsDataURL(blob);
  });
}
