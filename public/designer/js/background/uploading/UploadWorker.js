import { getFileDatabase } from '../../foundation/database/file.js';
import { createFile, finishUpload, getFile, startUpload, uploadChunk } from '../../foundation/api/files.js';

let subscription;
const oneMegaByte = 1024 * 1024;
let fileDatabase;

async function uploadFile({ id, name, tags, data }) {
  await fileDatabase.markFileUploading(id, name);
  try {
    const { id } = await createFile(name, tags);
    await startUpload(id);

    for (let i = 0; i < data.size; i += oneMegaByte) {
      await uploadChunk(id, i, data.slice(i, i + oneMegaByte));
    }

    await finishUpload(id);

    const uploadedFile = await getFile(id);
    await fileDatabase.saveFile(uploadedFile);
    await fileDatabase.markFileUploaded(id);
  } catch (error) {
    await fileDatabase.markFileUploaded(id);
    await fileDatabase.setUploadError(error, name);
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
