import Apex from '../../../../../lib/apex/apexcharts.esm.js';
import { getStatisticsByGroup } from '../../../api/statistics.js';
import localize from '../../../utils/localize.js';
import '../loader.js';

export class BaseDiagram extends HTMLElement {
  constructor(type, dataLabels) {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
    this.#type = type;
    this.#dataLabels = {
      ...dataLabels,
      style: {
        fontFamily: 'var(--font-family), sans-serif',
        fontWeight: 'var(--font-family-regular)',
        fontSize: '16px',
        color: 'var(--black)',
      },
    };
  }

  #type;
  #dataLabels;
  #apex = null;

  connectedCallback() {
    this.root.innerHTML = `
      <style>
          @import "/designer/lib/apex/apexcharts.css";
          @import "/designer/css/statistics.css";
          
          .content {
            display: grid;
            
            cms-loader {
              place-self: center center;
            }
          }
          
          .apexcharts-title-text {
            font-weight: var(--font-weight-light);
          }
      </style>
      <div class="content">
        <cms-loader id="loader"></cms-loader>
        <div id="diagram">
        </div>
      </div>`;
    this.renderDiagram();
  }

  static get observedAttributes() {
    return ['range', 'group'];
  }

  attributeChangedCallback(property, oldValue, newValue) {
    if (oldValue === newValue) {
      return;
    }

    this[property] = newValue;
  }

  get name() {
    return localize({ key: `statistics.access.${this.group.replace('-', '_')}` });
  }

  get range() {
    return parseInt(this.getAttribute('range'));
  }

  set range(value) {
    this.setAttribute('range', value);
    this.renderDiagram();
  }

  get group() {
    return this.getAttribute('group');
  }

  set group(value) {
    this.setAttribute('group', value);
    this.renderDiagram();
  }

  get version() {
    return this.hasAttribute('version');
  }

  set version(value) {
    if (value) {
      this.setAttribute('version', value);
    } else {
      this.removeAttribute('version');
    }

    this.renderDiagram();
  }

  getLabel(item) {
    return item.group;
  }

  getSeries(stats) {
    return [
      {
        name: this.name,
        data: stats
          .sort((a, b) => b.visits - a.visits)
          .map((item) => ({
            y: item.visits,
            x: this.getLabel(item),
          })),
      },
    ];
  }

  getLabels(stats) {
    return [];
  }

  getShadeIntensity() {
    return 0.1;
  }

  getLegend() {
    return {
      show: false,
    };
  }

  getOptions(stats) {
    return {
      legend: this.getLegend(),
      chart: {
        height: 360,
        type: this.#type,
        style: {
          fontSize: '16px',
          fontFamily: 'var(--font-family), sans-serif',
          fontWeight: 'var(--font-family-regular)',
          color: 'var(--black)',
        },
        toolbar: {
          show: false,
        },
      },
      theme: {
        monochrome: {
          enabled: true,
          color: '#19324c',
          shadeTo: 'light',
          shadeIntensity: this.getShadeIntensity(),
        },
      },
      plotOptions: {
        treemap: {
          distributed: true,
          enableShades: true,
        },
        bar: {
          borderRadius: 4,
          distributed: true,
          enableShades: true,
        },
      },
      dataLabels: this.#dataLabels,
      series: this.getSeries(stats),
      labels: this.getLabels(stats),
      title: {
        text: this.name,
        align: 'left',
        style: {
          fontFamily: 'var(--font-family-heading), sans-serif',
          fontWeight: 'var(--font-family-light)',
          fontSize: '24px',
          color: 'var(--black)',
        },
      },
      tooltip: {
        shared: false,
        style: {
          fontFamily: 'var(--font-family), sans-serif',
          fontWeight: 'var(--font-family-regular)',
          fontSize: '16px',
          color: 'var(--black)',
        },
        y: {
          formatter(item) {
            return item.toLocaleString();
          },
        },
      },
      yaxis: {
        labels: {
          formatter(item) {
            return item.toLocaleString();
          },
        },
        style: {
          fontFamily: 'var(--font-family), sans-serif',
          fontWeight: 'var(--font-family-regular)',
          fontSize: '12px',
          color: 'var(--black)',
        },
      },
      xaxis: {
        labels: {
          style: {
            fontFamily: 'var(--font-family), sans-serif',
            fontWeight: 'var(--font-family-regular)',
            fontSize: '12px',
            color: 'var(--black)',
          },
        },
      },
    };
  }

  async renderDiagram() {
    if (isNaN(this.range)) {
      return;
    }

    const stats = await getStatisticsByGroup(this.group, this.range);
    const options = this.getOptions(stats);
    if (!this.#apex) {
      this.#apex = new Apex(this.root.getElementById('diagram'), options);
      this.#apex.render();
      this.root.getElementById('loader').remove();
    } else {
      this.#apex.updateOptions(options);
    }
  }
}
