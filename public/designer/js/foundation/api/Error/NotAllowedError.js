import HttpError from './HttpError.js';

export default class NotAllowedError extends HttpError {
  constructor(error) {
    super(403, error);
  }
}
