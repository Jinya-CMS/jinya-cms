export default class TagPopupCloseEvent extends Event {
  constructor() {
    super('close', {
      bubbles: true,
      cancelable: false,
      composed: true,
    });
  }
}
