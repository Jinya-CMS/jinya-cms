import HttpError from './HttpError.js';

export default class ConflictError extends HttpError {
  constructor(error) {
    super(409, error);
  }
}
