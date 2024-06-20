import { getFileDatabase } from '../../foundation/database/file.js';
import { createFile } from '../../foundation/api/files.js';
import localize from '../../foundation/utils/localize.js';

let subscription;
let fileDatabase;

async function uploadFile({ id, name, tags, data }) {
  await fileDatabase.markFileUploading(id, name);
  try {
    const uploadedFile = await createFile(name, tags, data);

    await fileDatabase.saveFile(uploadedFile);
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
    if (subscription) {
      subscription.unsubscribe();
    }

    subscribe();
  }
};
