export default {
  changeTitle(title) {
    document.title = `${window.options.pageTitle} – ${title}`;
  }
}