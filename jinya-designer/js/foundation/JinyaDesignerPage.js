export default class JinyaDesignerPage {
  /**
   * Method to render the subclass and return the rendered HTML
   * @return string
   */
  // eslint-disable-next-line class-methods-use-this
  renderToString() {
    return '';
  }

  /**
   * Renders the current page into the main tag
   */
  renderToScreen() {
    document.getElementById('mainPage').innerHTML = this.renderToString();
    this.bindEvents();
  }

  /**
   * Method to bind the events needed
   * @return void
   */
  // eslint-disable-next-line class-methods-use-this
  bindEvents() {
  }
}
