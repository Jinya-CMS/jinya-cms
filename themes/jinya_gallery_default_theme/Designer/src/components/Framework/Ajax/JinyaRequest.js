import Lockr from 'lockr';
import NotFoundError from "@/components/Framework/Ajax/Error/NotFoundError";
import NotAllowedError from "@/components/Framework/Ajax/Error/NotAllowedError";
import UnauthorizedError from "@/components/Framework/Ajax/Error/UnauthorizedError";
import BadRequestError from "@/components/Framework/Ajax/Error/BadRequestError";

async function send(verb, url, data) {
  const request = {
    method: verb,
    headers: {
      JinyaApiKey: Lockr.get('JinyaApiKey')
    }
  };

  switch (verb.toLowerCase()) {
    case 'post':
    case 'put':
      request.body = data;
      break;
  }

  return await fetch(url, request).then(async response => {
    if (response.ok) {
      return response.json();
    } else {
      switch (response.status) {
        case 400:
          throw new BadRequestError(await response.json().then(error => error.message));
        case 401:
          throw new UnauthorizedError(await response.json().then(error => error.message));
        case 403:
          throw new NotAllowedError(await response.json().then(error => error.message));
        case 404:
          throw new NotFoundError(await response.json().then(error => error.message));
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