import HttpError from '@/framework/Ajax/Error/HttpError';

export default class UnauthorizedError extends HttpError {
    constructor(error) {
        super(401, error);
    }
}
