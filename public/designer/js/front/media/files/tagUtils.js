import Emojis from '../../../../lib/emojis.js';

const flattenedEmojis = Emojis.flatMap((m) => m.emojis).reduce(
  (previousValue, currentValue) => previousValue.concat(currentValue),
  [],
);

export function getRandomColor() {
  return `#${Math.floor(Math.random() * 16777215).toString(16)}`;
}

export function getRandomEmoji() {
  return flattenedEmojis[Math.floor(Math.random() * flattenedEmojis.length)];
}
