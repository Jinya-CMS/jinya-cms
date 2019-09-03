import HttpError from '@/framework/Ajax/Error/HttpError';

export default class NotFoundError extends HttpError {
    constructor(error) {
        super(404, error);
    }
}
