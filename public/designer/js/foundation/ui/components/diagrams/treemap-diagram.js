import localize from '../../../utils/localize.js';
import { BaseDiagram } from './base-diagram.js';

class TreemapDiagramElement extends BaseDiagram {
  constructor() {
    super('treemap', {
      formatter(text, { value }) {
        return `${text}: ${parseInt(value).toLocaleString()}`;
      },
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
    if (this.group === 'country') {
      return localize({ key: `countries.${item.group}` });
    }

    return super.getLabel(item);
  }
}

if (!customElements.get('cms-treemap-diagram')) {
  customElements.define('cms-treemap-diagram', TreemapDiagramElement);
}
