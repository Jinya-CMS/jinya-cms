import HttpError from './HttpError.js';

export default class BadRequestError extends HttpError {
  constructor(error) {
    super(400, error);
  }
}
