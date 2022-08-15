import {
  getDeviceCode, getJinyaApiKey, hasDeviceCode, hasJinyaApiKey,
} from '../storage.js';

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

  return fetch(url, request).then(async (response) => {
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
        // eslint-disable-next-line no-case-declarations
        const { default: BadRequestError } = await import('./Error/BadRequestError.js');
        throw new BadRequestError(httpError);
      case 401:
        // eslint-disable-next-line no-case-declarations
        const { default: UnauthorizedError } = await import('./Error/UnauthorizedError.js');
        throw new UnauthorizedError(httpError);
      case 403:
        // eslint-disable-next-line no-case-declarations
        const { default: NotAllowedError } = await import('./Error/NotAllowedError.js');
        throw new NotAllowedError(httpError);
      case 404:
        // eslint-disable-next-line no-case-declarations
        const { default: NotFoundError } = await import('./Error/NotFoundError.js');
        throw new NotFoundError(httpError);
      case 409:
        // eslint-disable-next-line no-case-declarations
        const { default: ConflictError } = await import('./Error/ConflictError.js');
        throw new ConflictError(httpError);
      default:
        // eslint-disable-next-line no-case-declarations
        const { default: HttpError } = await import('./Error/HttpError.js');
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

export function uploadPost(url, file) {
  return send('post', url, file, file.type);
}

export function getHost() {
  return '';
}
