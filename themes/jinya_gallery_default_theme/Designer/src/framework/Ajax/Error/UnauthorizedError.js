export default class UnauthorizedError extends Error {
  constructor(message) {
    super();
    this.message = message;
  }
}