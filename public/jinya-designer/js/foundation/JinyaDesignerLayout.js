export default class JinyaDesignerLayout {
  /**
   * Creates a new layout with the given layout template
   */
  constructor() {
    this.child = null;
  }

  /**
   * Method to render the subclass and return the rendered HTML
   * @return string
   */
  // eslint-disable-next-line class-methods-use-this
  toString() {
    return this.getTemplate().replace('%%%child%%%', this.child === null ? '' : this.child.toString());
  }

  /**
   * Renders the current page into the main tag
   * @return void
   */
  display() {
    document.getElementById('mainPage').innerHTML = this.toString();
    this.bindEvents();
    if (this.child !== null) {
      this.child.bindEvents();
    }
  }

  /**
   * Method to bind the events needed
   * @return void
   */
  // eslint-disable-next-line class-methods-use-this
  bindEvents() {
  }

  /**
   * Returns the template of the layout
   * @return string
   */
  // eslint-disable-next-line class-methods-use-this
  getTemplate() {
  }
}
