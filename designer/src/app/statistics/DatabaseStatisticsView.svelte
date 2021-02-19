<script>
  import ApexCharts from 'apexcharts';
  import { onMount } from "svelte";
  import { get } from "../../http/request";
  import { _ } from 'svelte-i18n';

  let entityShare;
  let fileHistory;
  let systemStats;

  onMount(async () => {
    await Promise.all([
      (async () => {
        const entityShareData = await get('/api/statistics/entity');
        const entityShareChart = new ApexCharts(entityShare, {
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
          ],
          labels: [
            $_('statistics.database.galleries'),
            $_('statistics.database.simple_pages'),
            $_('statistics.database.segment_pages'),
            $_('statistics.database.forms'),
          ],
        });
        await entityShareChart.render();
      })(),
      (async () => {
        const systemStatsData = await get('/api/statistics/system');
        const systemStatsChart = new ApexCharts(systemStats, {
          chart: {
            type: 'pie',
            height: 400,
            width: 500,
          },
          dataLabels: {
            formatter(val, {seriesIndex, w}) {
              return `${(w.config.series[seriesIndex] / 1024 / 1024 / 1024).toFixed(2)} GB`
            },
          },
          series: [
            systemStatsData.total - systemStatsData.free,
            systemStatsData.free,
          ],
          labels: [
            $_('statistics.system.used'),
            $_('statistics.system.free'),
          ],
        });
        await systemStatsChart.render();
      })(),
      (async () => {
        const fileHistoryData = await get('/api/statistics/history/file');
        const fileHistoryChart = new ApexCharts(fileHistory, {
          series: [
            {
              name: $_('statistics.database.history.created'),
              data: fileHistoryData.created.map((item) => ({x: new Date(item.date), y: item.count})),
            },
            {
              name: $_('statistics.database.history.updated'),
              data: fileHistoryData.updated.map((item) => ({x: new Date(item.date), y: item.count})),
            },
          ],
          chart: {
            height: 400,
            type: 'area',
            zoom: {
              enabled: false
            },
          },
          dataLabels: {
            enabled: false,
          },
          stroke: {
            curve: 'smooth',
          },
          title: {
            text: $_('statistics.database.history.file'),
            align: 'left',
          },
          xaxis: {
            type: 'datetime',
          }
        });
        await fileHistoryChart.render();
      })(),
    ]);
  });
</script>

<div class="jinya-stats__row">
    <div bind:this={entityShare}></div>
    <div bind:this={systemStats}></div>
</div>
<div bind:this={fileHistory}></div>