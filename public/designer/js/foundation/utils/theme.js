export default function getTheme() {
  if (document.body.classList.contains('is--light')) {
    return 'light';
  }
  if (document.body.classList.contains('is--dark')) {
    return 'dark';
  }

  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}
