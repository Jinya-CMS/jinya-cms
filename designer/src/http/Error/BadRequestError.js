import HttpError from './HttpError';

export default class BadRequestError extends HttpError {
  constructor(error) {
    super(400, error);
  }
}
