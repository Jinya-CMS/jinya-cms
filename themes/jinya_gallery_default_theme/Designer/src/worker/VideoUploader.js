import JinyaWorkerRequest from "@/framework/Worker/JinyaWorkerRequest";

// noinspection PointlessArithmeticExpressionJS Might change at some point
const chunkSize = 1 * 1024 * 1024;

async function startUpload(slug, apiKey) {
  try {
    console.log(`Start upload for slug ${slug}`);
    await JinyaWorkerRequest.post(`/api/video/${slug}/video`, {}, apiKey);
    console.log(`Started upload for slug ${slug}`);

    return true;
  } catch (e) {
    postMessage({
      message: 'background.video.exists',
      error: {
        type: 'exists'
      }
    });
    return false;
  }
}

onmessage = async e => {
  if (e.data?.video && e.data?.slug && e.data?.apiKey) {
    console.log('Received message with file to upload');

    /** @var File videoFile */
    const videoFile = e.data.video;
    const slug = e.data.slug;
    const apiKey = e.data.apiKey;

    console.log(apiKey);

    if (await startUpload(slug, apiKey)) {
      postMessage({message: 'background.video.upload_started', started: true});

      console.log(`Upload chunks of ${chunkSize} bytes size to the server`);

      let offset = 0;
      const uploadPromises = [];

      while (videoFile.size > offset) {
        const blob = videoFile.slice(offset, offset + chunkSize);

        console.log(`Uploading chunk from ${offset} to ${offset + chunkSize} for slug ${slug}`);
        uploadPromises.push(JinyaWorkerRequest.upload(`/api/video/${slug}/video/${offset}`, blob, apiKey));

        offset = offset + chunkSize;
      }

      await Promise.all(uploadPromises);
      console.log(`Uploaded file for slug ${slug}`);

      console.log(`Finish upload for slug ${slug}`);
      await JinyaWorkerRequest.put(`/api/video/${slug}/video/finish`, {}, apiKey);

      console.log(`Finished upload for slug ${slug} close worker now`);
      postMessage({message: 'background.video.uploaded', finished: true});
      close();
    }
  }
};