import HttpError from './HttpError.js';

export default class UnauthorizedError extends HttpError {
  constructor(error) {
    super(401, error);
  }
}
