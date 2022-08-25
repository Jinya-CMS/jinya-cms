import clearChildren from './html/clearChildren.js';

export default class JinyaDesignerLayout {
  /**
   * Creates a new layout with the given layout template
   */
  constructor() {
    this.child = null;
  }

  /**
   * Gets executed immediately after the rendering occured
   * @returns {Promise<void>}
   */
  // eslint-disable-next-line class-methods-use-this,no-empty-function
  async afterRender() {
  }

  /**
   * Method to render the subclass and return the rendered HTML
   * @return string
   */
  // eslint-disable-next-line class-methods-use-this
  async toString() {
    return (await this.getTemplate()).replace('%%%child%%%', this.child === null ? '' : this.child.toString());
  }

  /**
   * Renders the current page into the main tag
   * @return void
   */
  async display() {
    clearChildren({ parent: document.getElementById('mainPage') });
    document.getElementById('mainPage').innerHTML = await this.toString();
    await this.afterRender();
    this.bindEvents();
    if (this.child !== null) {
      await this.child.displayed();
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
   * @return Promise<string>
   */
  // eslint-disable-next-line class-methods-use-this,no-empty-function
  async getTemplate() {
  }
}
