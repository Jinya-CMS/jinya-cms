import HttpError from '@/framework/Ajax/Error/HttpError';

export default class ConflictError extends HttpError {
    constructor(error)
    {
        super(409, error);
    }
}
