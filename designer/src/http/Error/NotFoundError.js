import HttpError from './HttpError';

export default class NotFoundError extends HttpError {
  constructor(error) {
    super(404, error);
  }
}
