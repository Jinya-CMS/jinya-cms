<script>
  import ApexCharts from 'apexcharts';
  import { onMount } from "svelte";
  import { _ } from 'svelte-i18n';
  import { get } from "../../http/request";

  let country;
  let deviceBrand;
  let deviceType;
  let language;
  let operatingSystem;
  let browser;
  let totalVisits = 0;

  onMount(async () => {
    const chart = {
      type: 'bar',
      height: 400,
      width: '50%',
      style: {
        fontSize: '14px',
        fontFamily: 'Lato, sans-serif',
      },
    };
    const plotOptions = {
      bar: {
        borderRadius: 6,
        columnWidth: '45%',
        distributed: true,
      }
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
        totalVisits = countryStats.map(m => m.visitCount).reduce((previousValue, currentValue) => previousValue + currentValue, 0);
        const countryStatsChart = new ApexCharts(country, {
          chart: {...chart, width: '99%', type: 'treemap'},
          plotOptions: {
            treemap: {
              distributed: true,
              enableShades: false,
            },
          },
          dataLabels: {
            formatter(text, {value}) {
              return `${text}: ${value}`;
            },
          },
          legend,
          series: [
            {
              name: $_('statistics.access.country'),
              data: countryStats.sort((a, b) => b.visitCount - a.visitCount).map(item => ({
                y: item.visitCount,
                x: item.label,
              })),
            },
          ],
          title:
            {
              text: $_('statistics.access.country'),
              align: 'left',
              style:
                {
                  fontFamily: 'Lato, sans-serif',
                  color: 'var(--black)'
                }
              ,
            },
        });
        await countryStatsChart.render();
      })(),
      (async () => {
        const languageStats = await get('/api/statistics/visits/language');
        const languageStatsChart = new ApexCharts(language, {
          chart: {...chart, width: '32%'},
          plotOptions,
          dataLabels,
          legend,
          series: [
            {
              name: $_('statistics.access.language'),
              data: languageStats.map(item => item.visitCount),
            },
          ],
          xaxis: {
            categories: languageStats.map(item => item.label),
            style: {
              fontSize: '12px',
              fontFamily: 'Lato, sans-serif',
            },
          },
          labels: languageStats.map(item => item.label),
          title: {
            text: $_('statistics.access.language'),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)'
            },
          },
        });
        await languageStatsChart.render();
      })(),
      (async () => {
        const deviceBrandStats = await get('/api/statistics/visits/brand');
        const deviceBrandStatsChart = new ApexCharts(deviceBrand, {
          chart: {...chart, width: '32%'},
          plotOptions,
          dataLabels,
          legend,
          series: [
            {
              name: $_('statistics.access.device_brand'),
              data: deviceBrandStats.map(item => item.visitCount),
            },
          ],
          xaxis: {
            categories: deviceBrandStats.map(item => item.label),
            style: {
              fontSize: '12px',
              fontFamily: 'Lato, sans-serif',
            },
          },
          labels: deviceBrandStats.map(item => item.label),
          title: {
            text: $_('statistics.access.device_brand'),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)'
            },
          },
        });
        await deviceBrandStatsChart.render();
      })(),
      (async () => {
        const deviceTypeStats = await get('/api/statistics/visits/type');
        const deviceTypeStatsChart = new ApexCharts(deviceType, {
          chart: {
            type: 'pie',
            height: 400,
            width: '33%',
          },
          dataLabels: {
            distributed: true,
          },
          legend: {
            width: 100,
            fontFamily: 'Lato, sans-serif',
          },
          series: deviceTypeStats.filter(item => item.visitCount > 0).map(item => item.visitCount),
          labels: deviceTypeStats.filter(item => item.visitCount > 0).map(item => item.label),
          title: {
            text: $_('statistics.access.device_type'),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)'
            },
          },
        });
        await deviceTypeStatsChart.render();
      })(),
      (async () => {
        const browserStats = await get('/api/statistics/visits/browser');
        const browserStatsChart = new ApexCharts(browser, {
          chart,
          plotOptions,
          dataLabels,
          legend,
          series: [
            {
              name: $_('statistics.access.browser'),
              data: browserStats.map(item => item.visitCount),
            },
          ],
          xaxis: {
            categories: browserStats.map(item => item.label),
            style: {
              fontSize: '12px',
              fontFamily: 'Lato, sans-serif',
            },
          },
          labels: browserStats.map(item => item.label),
          title: {
            text: $_('statistics.access.browser'),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)'
            },
          },
        });
        await browserStatsChart.render();
      })(),
      (async () => {
        const operatingSystemStats = await get('/api/statistics/visits/os');
        const operatingSystemStatsChart = new ApexCharts(operatingSystem, {
          chart,
          plotOptions,
          dataLabels,
          legend,
          series: [
            {
              name: $_('statistics.access.operating_system'),
              data: operatingSystemStats.map(item => item.visitCount),
              style: {
                fontSize: '12px',
                fontFamily: 'Lato, sans-serif',
              },
            },
          ],
          xaxis: {
            categories: operatingSystemStats.map(item => item.label),
          },
          labels: operatingSystemStats.map(item => item.label),
          title: {
            text: $_('statistics.access.operating_system'),
            align: 'left',
            style: {
              fontFamily: 'Lato, sans-serif',
              color: 'var(--black)'
            },
          },
        });
        await operatingSystemStatsChart.render();
      })(),
    ]);
  });

  const to = new Date(Date.now());
  const from = new Date(Date.now());
  from.setMonth(to.getMonth() - 1);

  function getLang() {
    if (navigator.languages) {
      return navigator.languages[0];
    }

    return navigator.language;
  }
</script>

<span class="cosmo-title">{$_('statistics.access.title')} | {from.toLocaleDateString(getLang(), {
  year: 'numeric',
  month: '2-digit',
  day: '2-digit'
})}
    – {to.toLocaleDateString(getLang(), {year: 'numeric', month: '2-digit', day: '2-digit'})}
    | {totalVisits} {$_('statistics.access.total_visits')}</span>
<div bind:this={country}></div>
<div class="jinya-stats__row">
    <div bind:this={language}></div>
    <div bind:this={deviceBrand}></div>
    <div bind:this={deviceType}></div>
    <div bind:this={browser}></div>
    <div bind:this={operatingSystem}></div>
</div>