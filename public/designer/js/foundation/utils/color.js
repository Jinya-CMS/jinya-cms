export function lightenDarkenColor(color, percentage) {
  const col = parseInt(color.toString().replaceAll('#', ''), 16);

  return (
    ((col & 0x0000ff) + percentage) |
    ((((col >> 8) & 0x00ff) + percentage) << 8) |
    (((col >> 16) + percentage) << 16)
  ).toString(16);
}

export function getRandomColor() {
  return `#${Math.floor(Math.random() * 16777215)
    .toString(16)
    .padStart(6, '0')}`;
}
