import { getFileDatabase } from '../../foundation/database/file.js';
import { getMediaDatabase } from '../../foundation/database/media.js';
import { createFile } from '../../foundation/api/files.js';

let fileDatabase;
let mediaDatabase;

async function uploadFile({ id, name, tags, folderId, data }) {
  await fileDatabase.markFileUploading(id, name);
  try {
    const uploadedFile = await createFile(name, tags, folderId, data);
    uploadedFile.folderId = folderId;

    await mediaDatabase.saveFile(uploadedFile);
    await fileDatabase.markFileUploaded(id);
    await fileDatabase.setRecentUpload(uploadedFile.name);
  } catch (error) {
    await fileDatabase.markFileUploaded(id);
    await fileDatabase.setUploadError({ status: error.status }, name);
  }
}

async function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function worker() {
  let currentUploadCount = 0;

  while (true) {
    const nextUpload = await fileDatabase.getNextUpload();
    if (!nextUpload || currentUploadCount === 10) {
      await sleep(10);
      continue;
    }

    currentUploadCount += 1;
    uploadFile(nextUpload).then(() => {
      currentUploadCount -= 1;
    });
  }
}

onmessage = async (event) => {
  if (event.data?.verb === 'subscribe') {
    fileDatabase = getFileDatabase();
    mediaDatabase = getMediaDatabase();

    await worker();
  }
};
