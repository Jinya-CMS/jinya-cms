export class EditorChangeEvent extends Event {
  constructor(value) {
    super('change', {
      bubbles: true,
      cancelable: false,
      composed: true,
    });
    this.value = value;
  }
}
