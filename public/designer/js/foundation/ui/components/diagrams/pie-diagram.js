import localize from '../../../utils/localize.js';
import { DiagramBase } from './diagram-base.js';

class PieDiagramElement extends DiagramBase {
  static get observedAttributes() {
    return super.observedAttributes;
  }

  getOptions(stats) {
    const statsSorted = stats.toSorted((a, b) => b.visits - a.visits);
    const top10 = statsSorted.slice(0, 5);
    const rest = statsSorted.slice(5, statsSorted.length).reduce((acc, c) => acc + c.visits, 0);
    const prefix = this.getAttribute('prefix');

    const series = top10.map((item) => {
      let name = item.group;
      if (prefix) {
        name = localize({ key: `${prefix}.${item.group}` });
      } else if (!name) {
        name = localize({ key: this.getAttribute('empty') });
      }

      return {
        value: item.visits,
        name,
      };
    });
    if (rest > 0) {
      series.push({
        value: rest,
        name: localize({ key: `statistics.other` }),
      });
    }

    return {
      textStyle: {
        fontFamily: 'var(--font-family)',
        color: 'var(--black)',
      },
      tooltip: {
        trigger: 'item',
      },
      visualMap: {
        show: false,
        min: 0,
        max: Math.max(...stats.map((d) => d.visits), rest),
        inRange: {
          color: ['#eef2fd', '#1d3461'],
        },
      },
      series: [
        {
          type: 'pie',
          radius: '80%',
          avoidLabelOverlap: false,
          label: {
            show: true,
            color: 'var(--black)',
          },
          labelLine: {
            lineStyle: {
              color: 'var(--black)',
            },
            smooth: 0.2,
            length: 10,
            length2: 20,
          },
          roseType: 'radius',
          emphasis: {
            label: {
              show: true,
            },
          },
          data: series,
        },
      ],
    };
  }
}

if (!customElements.get('cms-pie-diagram')) {
  customElements.define('cms-pie-diagram', PieDiagramElement);
}
