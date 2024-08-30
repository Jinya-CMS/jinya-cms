import Emojis from '../../../lib/emojis.js';

const flattenedEmojis = Emojis.flatMap((m) => m.emojis).reduce(
  (previousValue, currentValue) => previousValue.concat(currentValue),
  [],
);

export function getRandomEmoji() {
  return flattenedEmojis[Math.floor(Math.random() * flattenedEmojis.length)];
}

export function slugify(text) {
  return text
    .normalize('NFKD')
    .toLowerCase()
    .replace(/[^\w\s-]/g, '-')
    .trim()
    .replace(/[-\s]+/g, '-');
}
