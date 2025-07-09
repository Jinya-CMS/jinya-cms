import localize from '../../../utils/localize.js';
import { BaseDiagram } from './base-diagram.js';

class BarDiagramElement extends BaseDiagram {
  constructor() {
    super('bar', {
      enabled: false,
    });
  }

  connectedCallback() {
    super.connectedCallback();
  }

  static get observedAttributes() {
    return BaseDiagram.observedAttributes;
  }

  attributeChangedCallback(property, oldValue, newValue) {
    return super.attributeChangedCallback(property, oldValue, newValue);
  }

  getLabel(item) {
    if (this.group === 'language') {
      return localize({ key: `languages.${item.group.toLowerCase()}` });
    }

    if (item.group) {
      return super.getLabel(item);
    }

    return localize({ key: `statistics.access.${this.group.replace('-', '_')}_unknown` });
  }
}

if (!customElements.get('cms-bar-diagram')) {
  customElements.define('cms-bar-diagram', BarDiagramElement);
}
