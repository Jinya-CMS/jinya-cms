class HttpError extends Error {
  constructor(status, error) {
    super();
    this.status = status;
    this.message = error.message;
    this.type = error.type;
  }
}

let apiKey = '';

async function sendRequest({
                             url,
                             verb,
                             data,
                             contentType,
                           }) {
  const headers = { JinyaApiKey: apiKey, ContentType: contentType };

  const request = {
    headers,
    credentials: 'same-origin',
    method: verb,
  };

  if (data) {
    if (data instanceof Blob) {
      request.body = data;
    } else {
      request.body = JSON.stringify(data);
    }
  }

  const response = await fetch(url, request);
  if (response.ok) {
    if (response.status !== 204) {
      return response.json();
    }

    return null;
  }

  const httpError = await response.json().then((error) => error.error);
  throw new HttpError(response.status, httpError);
}

const files = [];

async function wait({ time }) {
  return new Promise((resolve) => {
    setTimeout(resolve, time);
  });
}

onmessage = async (e) => {
  const { files: pushedFiles, apiKey: pushedApiKey } = e.data;
  files.push(...pushedFiles);
  apiKey = pushedApiKey;
};

(async () => {
  while (true) {
    try {
      if (files.length === 0) {
        // eslint-disable-next-line no-await-in-loop
        await wait({ time: 1000 });
        // eslint-disable-next-line no-continue
        continue;
      }

      const file = files.pop();
      // eslint-disable-next-line no-await-in-loop
      const postResult = await sendRequest({
        contentType: 'application/json',
        verb: 'POST',
        url: '/api/media/file',
        data: {
          name: file
            .name
            .split('.')
            .reverse()
            .slice(1)
            .reverse()
            .join('.'),
        },
      });
      // eslint-disable-next-line no-await-in-loop
      await sendRequest({
        verb: 'PUT',
        url: `/api/media/file/${postResult.id}/content`,
      });
      // eslint-disable-next-line no-await-in-loop
      await sendRequest({
        verb: 'PUT',
        url: `/api/media/file/${postResult.id}/content/0`,
        data: file,
      });
      // eslint-disable-next-line no-await-in-loop
      await sendRequest({
        verb: 'PUT',
        url: `/api/media/file/${postResult.id}/content/finish`,
      });
      // eslint-disable-next-line no-await-in-loop
      const uploadedFile = await sendRequest({
        verb: 'GET',
        url: `/api/media/file/${postResult.id}`,
      });
      postMessage({ type: 'file-uploaded', file: uploadedFile });
    } catch (e) {
      postMessage({ type: 'file-uploaded-failed' });
    }
  }
})();
