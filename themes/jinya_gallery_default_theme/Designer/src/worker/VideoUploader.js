import JinyaWorkerRequest from "@/framework/Worker/JinyaWorkerRequest";

// noinspection PointlessArithmeticExpressionJS Might change at some point
const chunkSize = 1 * 1024 * 1024 * 1024;

async function startUpload(slug, apiKey) {
  console.log(`Start upload for slug ${slug}`);
  await JinyaWorkerRequest.post(`/api/video/${slug}/video`, apiKey);
  console.log(`Started upload for slug ${slug}`);
}

onmessage = async e => {
  if (e.data?.video && e.data?.slug && e.data?.apiKey) {
    postMessage('background.video.upload_started');
    console.log('Received message with file to upload');

    const apiKey = e.data.apiKey;
    /** @var File videoFile */
    const videoFile = e.data.video;

    await startUpload(e.data.slug, apiKey);

    console.log('Upload chunks of 1MB size to the server');

    let offset = 0;
    const uploadPromises = [];

    while (videoFile.size > offset) {
      const blob = videoFile.slice(offset, offset + chunkSize);

      console.log(`Uploading chunk from ${offset} to ${offset + chunkSize} for slug ${slug}`);
      uploadPromises.push(JinyaWorkerRequest.upload(`/api/video/${e.data.slug}/video/${offset}`, blob, apiKey));
    }

    await Promise.all(uploadPromises);
    console.log(`Uploaded file for slug ${slug}`);

    console.log(`Finish upload for slug ${slug}`);
    await JinyaWorkerRequest.post(`/api/video/${e.data.slug}/video/finish`, {}, apiKey);

    console.log(`Finished upload for slug ${slug} close worker now`);
    postMessage('background.video.uploaded');
    close();
  }
};