export default class TagEvent extends Event {
  id = '';

  name = '';

  color = '';

  emoji = '';

  constructor(eventName, id, name, color, emoji) {
    super(eventName);
    this.id = id;
    this.name = name;
    this.color = color;
    this.emoji = emoji;
  }
}
