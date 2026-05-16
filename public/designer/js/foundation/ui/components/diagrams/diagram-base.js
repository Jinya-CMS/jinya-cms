import * as echarts from '../../../../../lib/echarts.js';
import localize from '../../../utils/localize.js';
import { getStatisticsByGroup } from '../../../api/statistics.js';

export class DiagramBase extends HTMLElement {
  #echarts = null;

  constructor() {
    super();

    this.root = this.attachShadow({ mode: 'closed' });
  }

  connectedCallback() {
    this.root.innerHTML = `
      <style>
          @import "/lib/cosmo/typography.css";
      
          .content {
            display: grid;
            
            jinya-loader {
              place-self: center center;
            }
          }

          #diagram {
            width: 100%;
            height: 20rem;
          }
      </style>
      <div class="content">
        <jinya-loader id="loader"></jinya-loader>
        <h2 id="title"></h2>
        <div id="diagram">
        </div>
      </div>`;
    this.renderDiagram();
  }

  disconnectedCallback() {
    if (this.#echarts && this.#echarts.dispose) {
      this.#echarts.dispose();
    }
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
    return localize({ key: this.getAttribute('name') });
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

  get title() {
    return this.getAttribute('title');
  }

  set title(value) {
    this.setAttribute('title', value);
    this.renderDiagram();
  }

  getOptions() {
    return {
      title: {
        show: false,
      },
      textStyle: {
        fontFamily: 'var(--font-family)',
        color: 'var(--black)',
      },
      tooltip: {
        trigger: 'axis',
      },
      color: ['#1d3461', '#2a4b8c', '#3661b6', '#577ecd', '#819ed9'],
      grid: {
        left: 48,
        right: 36,
        bottom: '0%',
        top: 16,
      },
    };
  }

  async renderDiagram() {
    if (isNaN(this.range)) {
      return;
    }

    const stats = await getStatisticsByGroup(this.group, this.range);
    const options = this.getOptions(stats);
    if (!this.#echarts) {
      this.#echarts = echarts.init(this.root.getElementById('diagram'), null, {
        renderer: 'svg',
      });
      this.root.getElementById('loader').remove();
    }
    this.root.getElementById('title').textContent = this.name;
    this.#echarts.setOption(options);
  }
}
