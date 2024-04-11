import Emojis from '../../../lib/emojis.js';

const flattenedEmojis = Emojis.flatMap((m) => m.emojis).reduce(
  (previousValue, currentValue) => previousValue.concat(currentValue),
  [],
);

export function getRandomEmoji() {
  return flattenedEmojis[Math.floor(Math.random() * flattenedEmojis.length)];
}
