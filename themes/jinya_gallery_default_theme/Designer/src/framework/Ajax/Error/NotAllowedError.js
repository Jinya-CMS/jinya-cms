export default class NotAllowedError extends Error {
  constructor(message) {
    super();
    this.message = message;
  }
}