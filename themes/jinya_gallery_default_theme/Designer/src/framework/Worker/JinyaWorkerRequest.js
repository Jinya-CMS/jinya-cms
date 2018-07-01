import HttpError from "@/framework/Ajax/Error/HttpError";
import BadRequestError from "@/framework/Ajax/Error/BadRequestError";
import ConflictError from "@/framework/Ajax/Error/ConflictError";
import NotAllowedError from "@/framework/Ajax/Error/NotAllowedError";
import UnauthorizedError from "@/framework/Ajax/Error/UnauthorizedError";
import NotFoundError from "@/framework/Ajax/Error/NotFoundError";

async function send(verb, url, data, contentType, apiKey) {
  const headers = {
    JinyaApiKey: apiKey,
    'Content-Type': contentType
  };

  const request = {
    headers: headers,
    credentials: 'same-origin',
    method: verb
  };

  if (data) {
    if (data instanceof Blob) {
      request.body = data;
    } else {
      request.body = JSON.stringify(data);
    }
  }

  return await fetch(url, request).then(async response => {
    if (response.ok) {
      if (response.status !== 204) {
        return response.json();
      }
    } else {
      const error = await response.json().then(error => error.error.message);

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
  async get(url, apiKey) {
    return await send('get', url, apiKey);
  },
  async head(url, apiKey) {
    return await send('head', url, apiKey);
  },
  async put(url, data, apiKey) {
    return await send('put', url, data, 'application/json', apiKey);
  },
  async post(url, data, apiKey) {
    return await send('post', url, data, 'application/json', apiKey);
  },
  async delete(url, apiKey) {
    return await send('delete', url, apiKey);
  },
  async upload(url, file, apiKey) {
    return await send('put', url, file, file.type, apiKey);
  },
  async send(verb, url, data, apiKey) {
    return await send(verb, url, data, 'application/json', apiKey);
  }
}