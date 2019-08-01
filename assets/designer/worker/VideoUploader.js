/* eslint-disable no-console */
import JinyaWorkerRequest from '@/framework/Worker/JinyaWorkerRequest';

// noinspection PointlessArithmeticExpressionJS Might change at some point
const chunkSize = 1 * 1024 * 1024;

async function startUpload(slug, apiKey) {
    try {
        console.log(`Start upload for slug ${slug}`);
        await JinyaWorkerRequest.post(`/api/video/jinya/${slug}/video`, {}, apiKey);
        console.log(`Started upload for slug ${slug}`);

        return true;
    } catch (e) {
        postMessage({
            message: 'background.video.exists',
            error: {
                type: 'exists',
            },
        });
        return false;
    }
}

async function chunkUpload(slug, videoFile, apiKey) {
    async function uploadChunk(offset) {
        const blob = videoFile.slice(offset, offset + chunkSize);

        if (blob.size > 0) {
            console.log(`Uploading chunk from ${offset} to ${offset + chunkSize} for slug ${slug}`);
            await JinyaWorkerRequest.upload(`/api/video/jinya/${slug}/video/${offset}`, blob, apiKey);
        }
    }

    let currentOffset = 0;

    do {
        const chunks = [];
        // eslint-disable-next-line no-plusplus
        for (let i = 0; i < 5; i++) {
            chunks.push(uploadChunk(currentOffset));
            currentOffset += chunkSize;
        }

        // eslint-disable-next-line no-await-in-loop
        await Promise.all(chunks);
    } while (currentOffset < videoFile.size);
}

onmessage = async (e) => {
    if (e.data?.video && e.data?.slug && e.data?.apiKey) {
        console.log('Received message with file to upload');

        const { video, slug, apiKey } = e.data;

        if (await startUpload(slug, apiKey)) {
            postMessage({ message: 'background.video.upload_started', started: true });

            console.log(`Upload chunks of ${chunkSize} bytes size to the server`);
            await chunkUpload(slug, video, apiKey, chunkSize * -1);
            console.log(`Uploaded file for slug ${slug}`);

            console.log(`Finish upload for slug ${slug}`);
            await JinyaWorkerRequest.put(`/api/video/jinya/${slug}/video/finish`, {}, apiKey);

            console.log(`Finished upload for slug ${slug} close worker now`);
            postMessage({ message: 'background.video.uploaded', finished: true });
            // eslint-disable-next-line no-restricted-globals
            close();
        }
    }
};
