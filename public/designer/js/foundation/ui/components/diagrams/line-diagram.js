import { DiagramBase } from './diagram-base.js';
import localize from '../../../utils/localize.js';

class LineDiagramElement extends DiagramBase {
  constructor() {
    super();
  }

  static get observedAttributes() {
    return DiagramBase.observedAttributes;
  }

  connectedCallback() {
    super.connectedCallback();
  }

  attributeChangedCallback(property, oldValue, newValue) {
    return super.attributeChangedCallback(property, oldValue, newValue);
  }

  getOptions(stats) {
    return {
      ...super.getOptions(stats),
      xAxis: [
        {
          type: 'category',
          boundaryGap: false,
          data: stats.map((s) => new Date(Date.parse(s.group)).toLocaleDateString()),
        },
      ],
      visualMap: {
        show: false,
        min: 0,
        max: Math.max(...stats.map((d) => d.visits)),
        inRange: {
          color: ['#819ed9', '#1d3461'],
        },
      },
      yAxis: [
        {
          type: 'value',
        },
      ],
      series: [
        {
          showSymbol: false,
          type: 'line',
          smooth: true,
          data: stats.map((s) => s.visits),
          name: localize({ key: `statistics.access.visits` }),
        },
      ],
    };
  }
}

if (!customElements.get('cms-line-diagram')) {
  customElements.define('cms-line-diagram', LineDiagramElement);
}
