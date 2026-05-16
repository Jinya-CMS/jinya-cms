import { getStatisticsByEntityAndId } from '../../../api/statistics.js';
import * as echarts from '../../../../../lib/echarts.js';
import localize from '../../../utils/localize.js';

class SparklineElement extends HTMLElement {
  #echarts = null;

  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
  }

  connectedCallback() {
    this.root.innerHTML = `
      <style>
          @import "/designer/css/statistics.css";
          
          #diagram {
            width: 250px;
            height: 35px;
          }
      </style>
      <div id="diagram"></div>`;
    this.renderDiagram();
  }

  disconnectedCallback() {
    if (this.#echarts && this.#echarts.dispose) {
      this.#echarts.dispose();
    }
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

  getOptions(stats) {
    return {
      title: {
        show: false,
      },
      tooltip: {
        show: false,
        trigger: 'none',
      },
      color: ['#1d3461', '#2a4b8c', '#3661b6', '#577ecd', '#819ed9'],
      grid: {
        width: 250,
        height: 35,
        left: 0,
        right: 0,
        bottom: 0,
        top: 0,
      },
      xAxis: [
        {
          show: false,
          type: 'category',
          boundaryGap: false,
          data: stats.map((s) => new Date(Date.parse(s.group)).toLocaleDateString()),
        },
      ],
      yAxis: [
        {
          show: false,
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

  async renderDiagram() {
    if (isNaN(this.entityId)) {
      return;
    }

    if (isNaN(this.range)) {
      return;
    }

    const stats = await getStatisticsByEntityAndId(this.type, this.entityId, this.range);
    const options = this.getOptions(stats);
    if (!this.#echarts) {
      this.#echarts = echarts.init(this.root.getElementById('diagram'), null, {
        renderer: 'svg',
      });
    }
    this.#echarts.setOption(options);
  }
}

if (!customElements.get('cms-sparkline')) {
  customElements.define('cms-sparkline', SparklineElement);
}
