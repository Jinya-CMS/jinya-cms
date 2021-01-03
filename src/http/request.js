import NotFoundError from './Error/NotFoundError';
import NotAllowedError from './Error/NotAllowedError';
import UnauthorizedError from './Error/UnauthorizedError';
import BadRequestError from './Error/BadRequestError';
import HttpError from './Error/HttpError';
import ConflictError from './Error/ConflictError';
import { getDeviceCode, getJinyaApiKey, hasDeviceCode, hasJinyaApiKey } from '../storage/authentication/storage';

export function send(verb, url, data, contentType, additionalHeaders = {}, plain = false) {
  const headers = { 'Content-Type': contentType, ...additionalHeaders };

  if (hasJinyaApiKey()) {
    headers.JinyaApiKey = getJinyaApiKey();
  }

  if (hasDeviceCode()) {
    headers.JinyaDeviceCode = getDeviceCode();
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

  return fetch(`${getHost()}${url}`, request).then(async (response) => {
    if (response.ok) {
      if (response.status !== 204) {
        if (plain) {
          return response.text();
        }
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

export function get(url) {
  return send('get', url);
}

export function getPlain(url) {
  return send('get', url, null, null, null, true);
}

export function head(url) {
  return send('head', url);
}

export function put(url, data) {
  return send('put', url, data, 'application/json');
}

export function post(url, data) {
  return send('post', url, data, 'application/json');
}

export function httpDelete(url) {
  return send('delete', url);
}

export function upload(url, file) {
  return send('put', url, file, file.type);
}

export function getHost() {
  return localStorage.getItem('/jinya/dev/host');
}
