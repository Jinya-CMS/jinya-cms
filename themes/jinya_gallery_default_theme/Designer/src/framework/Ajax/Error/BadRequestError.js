import HttpError from '@/framework/Ajax/Error/HttpError';

export default class BadRequestError extends HttpError {
  constructor(error) {
    super(400, error);
  }
}
