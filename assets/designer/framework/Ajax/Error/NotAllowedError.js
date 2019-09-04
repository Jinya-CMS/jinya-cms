import HttpError from '@/framework/Ajax/Error/HttpError';

export default class NotAllowedError extends HttpError {
    constructor(error) {
        super(403, error);
    }
}
