import HttpError from '@/framework/Ajax/Error/HttpError';
import BadRequestError from '@/framework/Ajax/Error/BadRequestError';
import ConflictError from '@/framework/Ajax/Error/ConflictError';
import NotAllowedError from '@/framework/Ajax/Error/NotAllowedError';
import UnauthorizedError from '@/framework/Ajax/Error/UnauthorizedError';
import NotFoundError from '@/framework/Ajax/Error/NotFoundError';

function send(verb, url, data, contentType, apiKey) {
  const headers = {
    JinyaApiKey: apiKey,
    'Content-Type': contentType,
  };

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

  return fetch(url, request).then(async (response) => {
    if (response.ok) {
      if (response.status !== 204) {
        return response.json();
      }

      return null;
    }

    const httpError = await response.json().then((error) => error.error.message);

    switch (response.status) {
      case 400:
        throw new BadRequestError(httpError);
      case 401:
        throw new UnauthorizedError(httpError);
      case 403:
        throw new NotAllowedError(httpError);
      case 404:
        throw new NotFoundError(httpError);
      case 409:
        throw new ConflictError(httpError);
      default:
        throw new HttpError(response.status, httpError);
    }
  });
}

export default {
  get(url, apiKey) {
    return send('get', url, apiKey);
  },
  head(url, apiKey) {
    return send('head', url, apiKey);
  },
  put(url, data, apiKey) {
    return send('put', url, data, 'application/json', apiKey);
  },
  post(url, data, apiKey) {
    return send('post', url, data, 'application/json', apiKey);
  },
  delete(url, apiKey) {
    return send('delete', url, apiKey);
  },
  upload(url, file, apiKey) {
    return send('put', url, file, file.type, apiKey);
  },
  send(verb, url, data, apiKey) {
    return send(verb, url, data, 'application/json', apiKey);
  },
};
