export default class JinyaDesignerPage {
  /**
   * Creates the page with the given layout
   * @param layout {JinyaDesignerLayout}
   */
  constructor({ layout }) {
    this.layout = layout;
  }

  /**
   * Method to render the subclass and return the rendered HTML
   * @return string
   */
  // eslint-disable-next-line class-methods-use-this
  toString() {
    return '';
  }

  /**
   * Renders the current page into the main tag
   * @return void
   */
  async display() {
    this.layout.child = this;
    await this.layout.display();
  }

  /**
   * Gets executed when the current page is rendered into the DOM
   * @return Promise<void>
   */
  // eslint-disable-next-line no-empty-function,class-methods-use-this
  async displayed() {}

  /**
   * Method to bind the events needed
   * @return void
   */
  // eslint-disable-next-line class-methods-use-this
  bindEvents() {}
}
