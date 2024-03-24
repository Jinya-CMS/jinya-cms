import ApexCharts from '../../../lib/apexcharts.esm.js';
import html from '../../../lib/jinya-html.js';
import { get } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';

export default class MatomoPage extends JinyaDesignerPage {
  constructor({ layout }) {
    super({ layout });
    this.to = new Date(Date.now());
    this.from = new Date(Date.now());
    this.from.setMonth(this.to.getMonth() - 1);
    this.totalVisits = 0;
  }

  toString() {
    return html`
      <div>
      <span class="cosmo-title"
      >${localize({ key: 'statistics.access.title' })} |
        ${this.from.toLocaleDateString(window.navigator.language, {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
    })}
        –
        ${this.to.toLocaleDateString(window.navigator.language, {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
    })}
        | <span id="stats-total-visits">${this.totalVisits}</span> ${localize({
      key: 'statistics.access.total_visits',
    })}</span
      >
        <div class="jinya-stats__row jinya-stats__row--one">
          <div id="country-chart"></div>
        </div>
        <div class="jinya-stats__row jinya-stats__row--third">
          <div id="language-chart"></div>
          <div id="device-brand-chart"></div>
          <div id="device-type-chart"></div>
        </div>
        <div class="jinya-stats__row jinya-stats__row--half">
          <div id="browser-chart"></div>
          <div id="os-chart"></div>
        </div>
      </div>`;
  }

  async displayed() {
    await super.displayed();
    const chart = {
      type: 'bar',
      height: 400,
      style: {
        fontSize: '14px',
        fontFamily: 'Lato, sans-serif',
      },
    };
    const plotOptions = {
      bar: {
        borderRadius: 6,
        distributed: true,
      },
    };
    const dataLabels = {
      enabled: false,
    };
    const legend = {
      show: false,
    };

    await Promise.all([
      (async () => {
        const countryStats = await get('/api/statistics/visits/country');
        this.totalVisits = countryStats
          .map((m) => m.visitCount)
          .reduce((previousValue, currentValue) => previousValue + currentValue, 0);
        const countryStatsChart = new ApexCharts(document.getElementById('country-chart'), {
          chart: {
            ...chart,
            type: 'treemap',
          },
          plotOptions: {
            treemap: {
              distributed: true,
              enableShades: false,
            },
          },
          dataLabels: {
            formatter(text, { value }) {
              return `${text}: ${value}`;
            },
          },
          legend,
          series: [
            {
              name: localize({ key: 'statistics.access.country' }),
              data: countryStats
                .sort((a, b) => b.visitCount - a.visitCount)
                .map((item) => ({
                  y: item.visitCount,
                  x: item.label,
                })),
            },
          ],
          title: {
            text: localize({ key: 'statistics.access.country' }),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)',
            },
          },
        });
        await countryStatsChart.render();
        document.getElementById('stats-total-visits').textContent = this.totalVisits;
      })(),
      (async () => {
        const languageStats = await get('/api/statistics/visits/language');
        const languageStatsChart = new ApexCharts(document.getElementById('language-chart'), {
          chart: { ...chart },
          plotOptions,
          dataLabels,
          legend,
          series: [
            {
              name: localize({ key: 'statistics.access.language' }),
              data: languageStats.map((item) => item.visitCount),
            },
          ],
          xaxis: {
            categories: languageStats.map((item) => item.label),
            style: {
              fontSize: '12px',
              fontFamily: 'Lato, sans-serif',
            },
          },
          labels: languageStats.map((item) => item.label),
          title: {
            text: localize({ key: 'statistics.access.language' }),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)',
            },
          },
        });
        await languageStatsChart.render();
      })(),
      (async () => {
        const deviceBrandStats = await get('/api/statistics/visits/brand');
        const deviceBrandStatsChart = new ApexCharts(document.getElementById('device-brand-chart'), {
          chart: { ...chart },
          plotOptions,
          dataLabels,
          legend,
          series: [
            {
              name: localize({ key: 'statistics.access.device_brand' }),
              data: deviceBrandStats.map((item) => item.visitCount),
            },
          ],
          xaxis: {
            categories: deviceBrandStats.map((item) => item.label),
            style: {
              fontSize: '12px',
              fontFamily: 'Lato, sans-serif',
            },
          },
          labels: deviceBrandStats.map((item) => item.label),
          title: {
            text: localize({ key: 'statistics.access.device_brand' }),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)',
            },
          },
        });
        await deviceBrandStatsChart.render();
      })(),
      (async () => {
        const deviceTypeStats = await get('/api/statistics/visits/type');
        const deviceTypeStatsChart = new ApexCharts(document.getElementById('device-type-chart'), {
          chart: {
            type: 'pie',
            height: 400,
          },
          dataLabels: {
            distributed: true,
          },
          legend: {
            width: 100,
            fontFamily: 'Lato, sans-serif',
          },
          series: deviceTypeStats.filter((item) => item.visitCount > 0)
            .map((item) => item.visitCount),
          labels: deviceTypeStats.filter((item) => item.visitCount > 0)
            .map((item) => item.label),
          title: {
            text: localize({ key: 'statistics.access.device_type' }),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)',
            },
          },
        });
        await deviceTypeStatsChart.render();
      })(),
      (async () => {
        const browserStats = await get('/api/statistics/visits/browser');
        const browserStatsChart = new ApexCharts(document.getElementById('browser-chart'), {
          chart,
          plotOptions,
          dataLabels,
          legend,
          series: [
            {
              name: localize({ key: 'statistics.access.browser' }),
              data: browserStats.map((item) => item.visitCount),
            },
          ],
          xaxis: {
            categories: browserStats.map((item) => item.label),
            style: {
              fontSize: '12px',
              fontFamily: 'Lato, sans-serif',
            },
          },
          labels: browserStats.map((item) => item.label),
          title: {
            text: localize({ key: 'statistics.access.browser' }),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)',
            },
          },
        });
        await browserStatsChart.render();
      })(),
      (async () => {
        const operatingSystemStats = await get('/api/statistics/visits/os');
        const operatingSystemStatsChart = new ApexCharts(document.getElementById('os-chart'), {
          chart,
          plotOptions,
          dataLabels,
          legend,
          series: [
            {
              name: localize({ key: 'statistics.access.operating_system' }),
              data: operatingSystemStats.map((item) => item.visitCount),
              style: {
                fontSize: '12px',
                fontFamily: 'Lato, sans-serif',
              },
            },
          ],
          xaxis: {
            categories: operatingSystemStats.map((item) => item.label),
          },
          labels: operatingSystemStats.map((item) => item.label),
          title: {
            text: localize({ key: 'statistics.access.operating_system' }),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)',
            },
          },
        });
        await operatingSystemStatsChart.render();
      })(),
    ]);
  }
}
