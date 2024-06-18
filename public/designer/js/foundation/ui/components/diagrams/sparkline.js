import Apex from '../../../../../lib/apex/apexcharts.esm.js';
import { getStatisticsByEntityAndId } from '../../../api/statistics.js';
import { BaseDiagram } from './base-diagram.js';

class SparklineElement extends HTMLElement {
  constructor() {
    super();
    this.root = this.attachShadow({ mode: 'closed' });
  }

  #apex = null;

  connectedCallback() {
    this.root.innerHTML = `
      <style>
          @import "/designer/lib/apex/apexcharts.css";
          @import "/designer/css/statistics.css";
      </style>
      <div id="diagram"></div>`;
    this.renderDiagram();
  }

  static get observedAttributes() {
    return ['entity-id', 'type'];
  }

  attributeChangedCallback(property, oldValue, newValue) {
    if (oldValue === newValue) {
      return;
    }

    if (property === 'entity-id') {
      this['entityId'] = newValue;
    } else {
      this[property] = newValue;
    }
  }

  get entityId() {
    return parseInt(this.getAttribute('entity-id'));
  }

  set entityId(value) {
    this.setAttribute('entity-id', value);
    this.renderDiagram();
  }

  get type() {
    return this.getAttribute('type');
  }

  set type(value) {
    this.setAttribute('type', value);
    this.renderDiagram();
  }

  get interval() {
    return this.getAttribute('interval');
  }

  set interval(value) {
    this.setAttribute('interval', value);
    this.renderDiagram();
  }

  get range() {
    const parsed = parseInt(this.getAttribute('range'));
    if (isNaN(parsed)) {
      return 1;
    }

    return parsed;
  }

  set range(value) {
    this.setAttribute('range', value);
    this.renderDiagram();
  }

  async renderDiagram() {
    if (isNaN(this.entityId)) {
      return;
    }

    if (isNaN(this.range)) {
      return;
    }

    const stats = await getStatisticsByEntityAndId(this.type, this.entityId, this.range);
    const options = {
      series: [
        {
          data: stats.map((item) => ({
            y: item.visits.toLocaleString(),
            x: item.group,
          })),
        },
      ],
      chart: {
        type: 'line',
        width: 250,
        height: 35,
        sparkline: {
          enabled: true,
        },
      },
      stroke: {
        curve: 'smooth',
      },
      theme: {
        monochrome: BaseDiagram.theme,
      },
      tooltip: {
        enabled: false,
      },
    };
    if (!this.#apex) {
      this.#apex = new Apex(this.root.getElementById('diagram'), options);
      this.#apex.render();
    } else {
      this.#apex.updateOptions(options);
    }
  }
}

if (!customElements.get('cms-sparkline')) {
  customElements.define('cms-sparkline', SparklineElement);
}
