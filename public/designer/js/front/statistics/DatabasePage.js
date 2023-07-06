import ApexCharts from '../../../lib/apexcharts.esm.js';
import html from '../../../lib/jinya-html.js';
import { get } from '../../foundation/http/request.js';
import JinyaDesignerPage from '../../foundation/JinyaDesignerPage.js';
import localize from '../../foundation/localize.js';

export default class DatabasePage extends JinyaDesignerPage {
  // eslint-disable-next-line class-methods-use-this
  toString() {
    return html`
        <div class="jinya-stats__row">
            <div id="entity-share-chart"></div>
            <div id="system-stats-chart"></div>
        </div>
        <div id="file-history-chart"></div>`;
  }

  async displayed() {
    await super.displayed();
    await Promise.all([
      (async () => {
        const entityShareData = await get('/api/statistics/entity');
        const entityShareChart = new ApexCharts(document.getElementById('entity-share-chart'), {
          chart: {
            type: 'pie',
            height: 400,
            width: 500,
          },
          series: [
            entityShareData.galleries,
            entityShareData.simplePages,
            entityShareData.segmentPages,
            entityShareData.forms,
            entityShareData.blogPosts,
            entityShareData.blogCategories,
          ],
          labels: [
            localize({ key: 'statistics.database.galleries' }),
            localize({ key: 'statistics.database.simple_pages' }),
            localize({ key: 'statistics.database.segment_pages' }),
            localize({ key: 'statistics.database.forms' }),
            localize({ key: 'statistics.database.blog_posts' }),
            localize({ key: 'statistics.database.blog_categories' }),
          ],
        });
        await entityShareChart.render();
      })(),
      (async () => {
        const systemStatsData = await get('/api/statistics/system');
        const
          systemStatsChart = new ApexCharts(document.getElementById('system-stats-chart'), {
            chart: {
              type: 'pie',
              height: 400,
              width: 500,
            },
            dataLabels: {
              formatter(val, { seriesIndex, w }) {
                return `${(w.config.series[seriesIndex] / 1024 / 1024 / 1024).toFixed(2)} GB`;
              },
            },
            series: [
              systemStatsData.total - systemStatsData.free,
              systemStatsData.free,
            ],
            labels: [
              localize({ key: 'statistics.system.used' }),
              localize({ key: 'statistics.system.free' }),
            ],
          });
        await systemStatsChart.render();
      })(),
      (async () => {
        const fileHistoryData = await get('/api/statistics/history/file');
        const fileHistoryChart = new ApexCharts(document.getElementById('file-history-chart'), {
          series: [
            {
              name: localize({ key: 'statistics.database.history.created' }),
              data: fileHistoryData.created.map((item) => ({ x: new Date(item.date), y: item.count })),
            },
            {
              name: localize({ key: 'statistics.database.history.updated' }),
              data: fileHistoryData.updated.map((item) => ({ x: new Date(item.date), y: item.count })),
            },
          ],
          chart: {
            height: 400,
            type: 'area',
            zoom: {
              enabled: false,
            },
          },
          dataLabels: {
            enabled: false,
          },
          stroke: {
            curve: 'smooth',
          },
          title: {
            text: localize({ key: 'statistics.database.history.file' }),
            align: 'left',
          },
          xaxis: {
            type: 'datetime',
          },
        });
        await fileHistoryChart.render();
      })(),
    ]);
  }
}
