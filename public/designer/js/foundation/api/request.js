import { getAuthenticationDatabase } from '../database/authentication.js';

const authenticationDatabase = getAuthenticationDatabase();

export async function send(
  verb,
  url,
  data = undefined,
  contentType = 'application/json',
  additionalHeaders = {},
  plain = false,
) {
  const headers = { 'Content-Type': contentType, ...additionalHeaders };
  const apiKey = await authenticationDatabase.getApiKey();
  const deviceCode = await authenticationDatabase.getDeviceCode();

  if (apiKey) {
    headers.JinyaApiKey = apiKey;
  }

  if (deviceCode) {
    headers.JinyaDeviceCode = deviceCode;
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

  const response = await fetch(url, request);
  if (response.ok) {
    if (response.status !== 204) {
      if (plain) {
        return await response.text();
      }

      return await response.json();
    }

    return null;
  }

  const httpError = (await response.json()).error;
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
  if (data) {
    return send('put', url, data);
  }

  return send('put', url, data, '');
}

export function post(url, data) {
  if (data) {
    return send('post', url, data);
  }

  return send('post', url, data, '');
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
