import NotFoundError from '@/framework/Ajax/Error/NotFoundError';
import NotAllowedError from '@/framework/Ajax/Error/NotAllowedError';
import UnauthorizedError from '@/framework/Ajax/Error/UnauthorizedError';
import BadRequestError from '@/framework/Ajax/Error/BadRequestError';
import HttpError from '@/framework/Ajax/Error/HttpError';
import EventBus from '../Events/EventBus';
import Events from '../Events/Events';
import ConflictError from './Error/ConflictError';
import { getApiKey } from '@/framework/Storage/AuthStorage';

async function send(verb, url, data, contentType, additionalHeaders = {}) {
  EventBus.$emit(Events.request.started);
  const headers = Object.assign({
    JinyaApiKey: getApiKey(),
    'Content-Type': contentType,
  }, additionalHeaders);

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

  return await fetch(url, request).then(async (response) => {
    EventBus.$emit(Events.request.finished, { success: response.ok });

    if (response.ok) {
      if (response.status !== 204) {
        return response.json();
      }
    } else {
      const error = await response.json().then(error => error.error);

      switch (response.status) {
        case 400:
          throw new BadRequestError(error);
        case 401:
          throw new UnauthorizedError(error);
        case 403:
          throw new NotAllowedError(error);
        case 404:
          throw new NotFoundError(error);
        case 409:
          throw new ConflictError(error);
        default:
          throw new HttpError(response.status, error);
      }
    }
  });
}

export default {
  async get(url) {
    return await send('get', url);
  },
  async head(url) {
    return await send('head', url);
  },
  async put(url, data) {
    return await send('put', url, data, 'application/json');
  },
  async post(url, data) {
    return await send('post', url, data, 'application/json');
  },
  async delete(url) {
    return await send('delete', url);
  },
  async upload(url, file) {
    return await send('put', url, file, file.type);
  },
  async send(verb, url, data, additionalHeaders = {}) {
    return await send(verb, url, data, 'application/json', additionalHeaders);
  },
};
