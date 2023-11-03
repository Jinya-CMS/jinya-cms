export default class TagPopupSubmitEvent extends Event {
  name = '';

  emoji = '';

  color = '';

  constructor(name, color, emoji) {
    super('submit', {
      bubbles: true,
      cancelable: false,
      composed: true,
    });
    this.name = name;
    this.emoji = emoji;
    this.color = color;
  }
}
