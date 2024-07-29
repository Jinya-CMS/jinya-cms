import localize from '../../../utils/localize.js';
import { BaseDiagram } from './base-diagram.js';

class PieDiagramElement extends BaseDiagram {
  constructor() {
    super('pie', {
      enabled: true,
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

  getSeries(stats) {
    return stats.map((item) => item.visits);
  }

  getLabels(stats) {
    return stats.map((item) => this.getLabel(item));
  }

  getShadeIntensity() {
    return 0.8;
  }

  getLegend() {
    return {
      show: true,
      fontFamily: 'var(--font-family)',
      fontWeight: 'var(--font-weight-regular)',
      position: 'bottom',
      horizontalAlign: 'left',
    };
  }

  getLabel(item) {
    if (this.group === 'type') {
      return localize({ key: `statistics.device_type.${item.group}` });
    }

    return super.getLabel(item);
  }
}

if (!customElements.get('cms-pie-diagram')) {
  customElements.define('cms-pie-diagram', PieDiagramElement);
}
