import HttpError from './HttpError';

export default class ConflictError extends HttpError {
  constructor(error) {
    super(409, error);
  }
}
