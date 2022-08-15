import HttpError from './HttpError.js';

export default class NotFoundError extends HttpError {
  constructor(error) {
    super(404, error);
  }
}
