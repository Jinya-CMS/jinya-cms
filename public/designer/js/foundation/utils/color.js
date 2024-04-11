export function lightenDarkenColor(color, percentage) {
  const col = parseInt(color.toString().replaceAll('#', ''), 16);
  // eslint-disable-next-line no-bitwise
  return (
    // eslint-disable-next-line no-bitwise
    (
      ((col & 0x0000ff) + percentage) | // eslint-disable-next-line no-bitwise
      ((((col >> 8) & 0x00ff) + percentage) << 8) | // eslint-disable-next-line no-bitwise
      (((col >> 16) + percentage) << 16)
    ).toString(16)
  );
}

export function getRandomColor() {
  return `#${Math.floor(Math.random() * 16777215).toString(16)}`;
}
