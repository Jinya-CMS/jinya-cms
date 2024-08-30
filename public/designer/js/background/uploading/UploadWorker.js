import { getFileDatabase } from '../../foundation/database/file.js';
import { getMediaDatabase } from '../../foundation/database/media.js';
import { createFile } from '../../foundation/api/files.js';

let subscription;
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

function subscribe() {
  subscription = fileDatabase.watchPendingUploads().subscribe({
    next: async (files) => {
      subscription.unsubscribe();
      for (const file of files) {
        await uploadFile(file);
      }
      subscribe();
    },
  });
}

onmessage = (event) => {
  if (event.data?.verb === 'subscribe') {
    fileDatabase = getFileDatabase();
    mediaDatabase = getMediaDatabase();
    if (subscription) {
      subscription.unsubscribe();
    }

    subscribe();
  }
};
