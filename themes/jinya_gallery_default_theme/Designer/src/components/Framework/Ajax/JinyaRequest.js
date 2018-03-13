import Lockr from 'lockr';
import NotFoundError from "@/components/Framework/Ajax/Error/NotFoundError";
import NotAllowedError from "@/components/Framework/Ajax/Error/NotAllowedError";
import UnauthorizedError from "@/components/Framework/Ajax/Error/UnauthorizedError";
import BadRequestError from "@/components/Framework/Ajax/Error/BadRequestError";
import HttpError from "@/components/Framework/Ajax/Error/HttpError";

async function send(verb, url, data, contentType = 'application/json') {
  const request = {
    method: verb,
    headers: {
      JinyaApiKey: Lockr.get('JinyaApiKey'),
      'Content-Type': contentType
    }
  };

  switch (verb.toLowerCase()) {
    case 'post':
    case 'put':
      request.body = JSON.stringify(data);
      break;
  }

  return await fetch(url, request).then(async response => {
    if (response.ok) {
      return response.json();
    } else {
      const message = await response.json().then(error => error.message);
      switch (response.status) {
        case 400:
          throw new BadRequestError(message);
        case 401:
          throw new UnauthorizedError(message);
        case 403:
          throw new NotAllowedError(message);
        case 404:
          throw new NotFoundError(message);
        default:
          throw new HttpError(response.status, message);
      }
    }
  }).catch(reason => {
    return {success: false, reason: reason};
  })
}

export default {
  async get(url) {
    return await send('get', url);
  },
  async put(url, data) {
    return await send('put', url, data);
  },
  async post(url, data) {
    return await send('post', url, data);
  },
  async delete(url) {
    return await send('delete', url);
  }
}