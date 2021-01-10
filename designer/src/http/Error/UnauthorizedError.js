import HttpError from './HttpError';

export default class UnauthorizedError extends HttpError {
  constructor(error) {
    super(401, error);
  }
}
