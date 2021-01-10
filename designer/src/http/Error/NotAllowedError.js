import HttpError from './HttpError';

export default class NotAllowedError extends HttpError {
  constructor(error) {
    super(403, error);
  }
}
