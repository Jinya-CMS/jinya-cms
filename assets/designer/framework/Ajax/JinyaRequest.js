import NotFoundError from '@/framework/Ajax/Error/NotFoundError';
import NotAllowedError from '@/framework/Ajax/Error/NotAllowedError';
import UnauthorizedError from '@/framework/Ajax/Error/UnauthorizedError';
import BadRequestError from '@/framework/Ajax/Error/BadRequestError';
import HttpError from '@/framework/Ajax/Error/HttpError';
import ConflictError from '@/framework/Ajax/Error/ConflictError';
import EventBus from '@/framework/Events/EventBus';
import Events from '@/framework/Events/Events';
import { getApiKey } from '@/framework/Storage/AuthStorage';

function send(verb, url, data, contentType, additionalHeaders = {}) {
  EventBus.$emit(Events.request.started);
  const headers = { 'Content-Type': contentType, ...additionalHeaders };

  if (getApiKey()) {
    headers.JinyaApiKey = getApiKey();
  }

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
    EventBus.$emit(Events.request.finished, { success: response.ok });

    if (response.ok) {
      if (response.status !== 204) {
        return response.json();
      }

      return null;
    }

    const httpError = await response.json().then((error) => error.error);

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
  get(url) {
    return send('get', url);
  },
  head(url) {
    return send('head', url);
  },
  put(url, data) {
    return send('put', url, data, 'application/json');
  },
  post(url, data) {
    return send('post', url, data, 'application/json');
  },
  delete(url) {
    return send('delete', url);
  },
  upload(url, file) {
    return send('put', url, file, file.type);
  },
  send(verb, url, data, additionalHeaders = {}) {
    return send(verb, url, data, 'application/json', additionalHeaders);
  },
};
