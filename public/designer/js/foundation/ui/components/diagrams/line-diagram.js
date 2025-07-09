import localize from '../../../utils/localize.js';
import { BaseDiagram } from './base-diagram.js';

class LineDiagramElement extends BaseDiagram {
  constructor() {
    super('line', null);
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

  get interval() {
    return this.getAttribute('interval');
  }

  set interval(value) {
    this.setAttribute('interval', value);
    this.renderDiagram();
  }

  get name() {
    return localize({ key: `statistics.access.visits` });
  }

  getOptions(stats) {
    return {
      series: [
        {
          name: this.name,
          data: stats.map((item) => ({
            y: item.visits.toLocaleString(),
            x: item.group,
          })),
        },
      ],
      chart: {
        type: 'area',
        stacked: false,
        height: 360,
        toolbar: {
          show: false,
        },
        zoom: {
          enabled: false,
        },
        style: BaseDiagram.chartStyle,
      },
      dataLabels: {
        enabled: false,
      },
      markers: {
        size: 0,
      },
      title: {
        text: localize({ key: `statistics.access.${this.group.replace('-', '_')}.${this.interval.replace('-', '_')}` }),
        align: 'left',
        style: BaseDiagram.titleStyle,
      },
      theme: {
        monochrome: BaseDiagram.theme,
      },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          inverseColors: false,
          opacityFrom: 0.5,
          opacityTo: 0,
          stops: [0, 90, 100],
        },
      },
      yaxis: {
        labels: {
          formatter(item) {
            return item.toLocaleString();
          },
        },
        style: BaseDiagram.axisStyle,
      },
      xaxis: {
        type: 'datetime',
        labels: {
          style: BaseDiagram.axisStyle,
        },
      },
      tooltip: {
        shared: false,
        style: BaseDiagram.tooltipStyle,
        y: {
          formatter(item) {
            return item.toLocaleString();
          },
        },
      },
    };
  }
}

if (!customElements.get('cms-line-diagram')) {
  customElements.define('cms-line-diagram', LineDiagramElement);
}
