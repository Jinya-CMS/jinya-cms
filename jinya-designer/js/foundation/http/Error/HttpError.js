export default class HttpError extends Error {
  constructor(status, error) {
    super();
    this.status = status;
    this.message = error.message;
    this.type = error.type;
    this.plainError = error;
  }
}
