import localize from '../../../utils/localize.js';
import { getStatisticsByGroup } from '../../../api/statistics.js';
import * as echarts from '../../../../../lib/echarts.js';
import { get } from '../../../api/request.js';

const echartsCountryMapping = {
  AF: 'Afghanistan',
  AX: 'Aland',
  AL: 'Albania',
  DZ: 'Algeria',
  AS: 'American Samoa',
  AD: 'Andorra',
  AO: 'Angola',
  AG: 'Antigua and Barb.',
  AR: 'Argentina',
  AM: 'Armenia',
  AU: 'Australia',
  AT: 'Austria',
  AZ: 'Azerbaijan',
  BS: 'Bahamas',
  BH: 'Bahrain',
  BD: 'Bangladesh',
  BB: 'Barbados',
  BY: 'Belarus',
  BE: 'Belgium',
  BZ: 'Belize',
  BJ: 'Benin',
  BM: 'Bermuda',
  BT: 'Bhutan',
  BO: 'Bolivia',
  BA: 'Bosnia and Herz.',
  BW: 'Botswana',
  IO: 'Br. Indian Ocean Ter.',
  BR: 'Brazil',
  BN: 'Brunei',
  BG: 'Bulgaria',
  BF: 'Burkina Faso',
  BI: 'Burundi',
  KH: 'Cambodia',
  CM: 'Cameroon',
  CA: 'Canada',
  CV: 'Cape Verde',
  KY: 'Cayman Is.',
  CF: 'Central African Rep.',
  TD: 'Chad',
  CL: 'Chile',
  CN: 'China',
  CO: 'Colombia',
  KM: 'Comoros',
  CG: 'Congo',
  CD: 'Dem. Rep. Congo',
  CR: 'Costa Rica',
  CI: "Côte d'Ivoire",
  HR: 'Croatia',
  CU: 'Cuba',
  CW: 'Curaçao',
  CY: 'Cyprus',
  CZ: 'Czech Rep.',
  KP: 'Dem. Rep. Korea',
  DK: 'Denmark',
  DJ: 'Djibouti',
  DM: 'Dominica',
  DO: 'Dominican Rep.',
  EC: 'Ecuador',
  EG: 'Egypt',
  SV: 'El Salvador',
  GQ: 'Eq. Guinea',
  ER: 'Eritrea',
  EE: 'Estonia',
  SZ: 'Swaziland', // Eswatini in ISO, but ECharts uses "Swaziland"
  ET: 'Ethiopia',
  FK: 'Falkland Is.',
  FO: 'Faeroe Is.',
  FJ: 'Fiji',
  FI: 'Finland',
  FR: 'France',
  PF: 'Fr. Polynesia',
  TF: 'Fr. S. Antarctic Lands',
  GA: 'Gabon',
  GM: 'Gambia',
  GE: 'Georgia',
  DE: 'Germany',
  GH: 'Ghana',
  GR: 'Greece',
  GL: 'Greenland',
  GD: 'Grenada',
  GU: 'Guam',
  GT: 'Guatemala',
  GN: 'Guinea',
  GW: 'Guinea-Bissau',
  GY: 'Guyana',
  HT: 'Haiti',
  HM: 'Heard I. and McDonald Is.',
  HN: 'Honduras',
  HU: 'Hungary',
  IS: 'Iceland',
  IN: 'India',
  ID: 'Indonesia',
  IR: 'Iran',
  IQ: 'Iraq',
  IE: 'Ireland',
  IM: 'Isle of Man',
  IL: 'Israel',
  IT: 'Italy',
  JM: 'Jamaica',
  JP: 'Japan',
  JE: 'Jersey',
  JO: 'Jordan',
  KZ: 'Kazakhstan',
  KE: 'Kenya',
  KI: 'Kiribati',
  KR: 'Korea',
  KW: 'Kuwait',
  KG: 'Kyrgyzstan',
  LA: 'Lao PDR',
  LV: 'Latvia',
  LB: 'Lebanon',
  LS: 'Lesotho',
  LR: 'Liberia',
  LY: 'Libya',
  LI: 'Liechtenstein',
  LT: 'Lithuania',
  LU: 'Luxembourg',
  MK: 'Macedonia', // North Macedonia in ISO, ECharts uses "Macedonia"
  MG: 'Madagascar',
  MW: 'Malawi',
  MY: 'Malaysia',
  MV: 'Maldives',
  ML: 'Mali',
  MT: 'Malta',
  MR: 'Mauritania',
  MU: 'Mauritius',
  MX: 'Mexico',
  FM: 'Micronesia',
  MD: 'Moldova',
  MC: 'Monaco',
  MN: 'Mongolia',
  ME: 'Montenegro',
  MS: 'Montserrat',
  MA: 'Morocco',
  MZ: 'Mozambique',
  MM: 'Myanmar',
  NA: 'Namibia',
  NP: 'Nepal',
  NL: 'Netherlands',
  NC: 'New Caledonia',
  NZ: 'New Zealand',
  NI: 'Nicaragua',
  NE: 'Niger',
  NG: 'Nigeria',
  NU: 'Niue',
  MP: 'N. Mariana Is.',
  NO: 'Norway',
  OM: 'Oman',
  PK: 'Pakistan',
  PW: 'Palau',
  PS: 'Palestine',
  PA: 'Panama',
  PG: 'Papua New Guinea',
  PY: 'Paraguay',
  PE: 'Peru',
  PH: 'Philippines',
  PL: 'Poland',
  PT: 'Portugal',
  PR: 'Puerto Rico',
  QA: 'Qatar',
  RO: 'Romania',
  RU: 'Russia',
  RW: 'Rwanda',
  SH: 'Saint Helena',
  LC: 'Saint Lucia',
  PM: 'St. Pierre and Miquelon',
  VC: 'St. Vin. and Gren.',
  WS: 'Samoa',
  ST: 'São Tomé and Principe',
  SA: 'Saudi Arabia',
  SN: 'Senegal',
  RS: 'Serbia',
  SC: 'Seychelles',
  SL: 'Sierra Leone',
  SG: 'Singapore',
  SK: 'Slovakia',
  SI: 'Slovenia',
  SB: 'Solomon Is.',
  SO: 'Somalia',
  ZA: 'South Africa',
  GS: 'S. Geo. and S. Sandw. Is.',
  SS: 'S. Sudan',
  ES: 'Spain',
  LK: 'Sri Lanka',
  SD: 'Sudan',
  SR: 'Suriname',
  SE: 'Sweden',
  CH: 'Switzerland',
  SY: 'Syria',
  TW: 'Taiwan',
  TJ: 'Tajikistan',
  TZ: 'Tanzania',
  TH: 'Thailand',
  TL: 'Timor-Leste',
  TG: 'Togo',
  TO: 'Tonga',
  TT: 'Trinidad and Tobago',
  TN: 'Tunisia',
  TR: 'Turkey',
  TM: 'Turkmenistan',
  TC: 'Turks and Caicos Is.',
  VI: 'U.S. Virgin Is.',
  UG: 'Uganda',
  UA: 'Ukraine',
  AE: 'United Arab Emirates',
  GB: 'United Kingdom',
  US: 'United States',
  UY: 'Uruguay',
  UZ: 'Uzbekistan',
  VU: 'Vanuatu',
  VE: 'Venezuela',
  VN: 'Vietnam',
  EH: 'W. Sahara',
  YE: 'Yemen',
  ZM: 'Zambia',
  ZW: 'Zimbabwe',
};

class WorldMapElement extends HTMLElement {
  constructor() {
    super();
    this.root = this.attachShadow({ mode: 'closed' });
  }

  #echartsMap;
  #echartsDonut;

  get name() {
    return localize({ key: this.getAttribute('name') });
  }

  get range() {
    return parseInt(this.getAttribute('range'));
  }

  set range(value) {
    this.setAttribute('range', value);
    this.#renderAll();
  }

  get group() {
    return this.getAttribute('group');
  }

  set group(value) {
    this.setAttribute('group', value);
    this.#renderAll();
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

    this.#renderAll();
  }

  get title() {
    return this.getAttribute('title');
  }

  set title(value) {
    this.setAttribute('title', value);
    this.#renderAll();
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

          .diagrams {
            display: grid;
            grid-template-columns: 50% 50%;
            height: 30rem;
          }
          
          #map,
          #donut {
            width: 100%;
            height: 30rem;
          }
          
      </style>
      <div class="content">
        <jinya-loader id="loader"></jinya-loader>
        <h2 id="title"></h2>
        <div class="diagrams">
          <div id="map"></div>
          <div id="donut"></div>
        </div>
      </div>`;
    this.#renderAll();
  }

  disconnectedCallback() {
    if (this.#echartsMap && this.#echartsMap.dispose) {
      this.#echartsMap.dispose();
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

  #getMapOptions(stats) {
    const seriesData = stats.map((d) => ({
      name: echartsCountryMapping[d.group],
      value: d.visits,
    }));

    return {
      nameMap: echartsCountryMapping,
      visualMap: {
        show: false,
        min: 0,
        max: Math.max(...stats.map((d) => d.visits)),
        inRange: {
          color: ['#eef2fd', '#1d3461'],
        },
      },
      series: [
        {
          type: 'map',
          map: 'world',
          roam: true,
          emphasis: {
            label: { show: false },
          },
          nameMap: echartsCountryMapping,
          data: seriesData,
        },
      ],
      tooltip: {
        trigger: 'item',
        formatter: ({ name, value }) => {
          const iso = Object.keys(echartsCountryMapping).find((k) => echartsCountryMapping[k] === name);

          return `${localize({ key: `countries.${iso}` })} ${isNaN(value) ? 0 : value}`;
        },
      },
    };
  }

  #getDonutOptions(stats) {
    const statsSorted = stats.toSorted((a, b) => b.visits - a.visits);
    const top5 = statsSorted.slice(0, 5);
    const rest = statsSorted.slice(5, statsSorted.length).reduce((acc, c) => acc + c.visits, 0);
    const series = top5.map((item) => ({
      value: item.visits,
      name: localize({ key: `countries.${item.group}` }),
    }));
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

  async #renderAll() {
    if (isNaN(this.range)) {
      return;
    }
    const stats = await getStatisticsByGroup(this.group, this.range);
    await Promise.all([this.#renderMap(stats), this.#renderDonut(stats)]);
    this.root.getElementById('title').textContent = this.name;
    this.root.getElementById('loader').remove();
  }

  async #renderMap(stats) {
    if (!echarts.getMap('world')) {
      const worldmap = await get('/designer/js/foundation/ui/components/diagrams/world.json');
      echarts.registerMap('world', worldmap);
    }

    const options = this.#getMapOptions(stats);
    if (!this.#echartsMap) {
      this.#echartsMap = echarts.init(this.root.getElementById('map'), null, {
        renderer: 'svg',
      });
    }
    this.#echartsMap.setOption(options);
  }

  async #renderDonut(stats) {
    const options = this.#getDonutOptions(stats);
    if (!this.#echartsDonut) {
      this.#echartsDonut = echarts.init(this.root.getElementById('donut'), null, {
        renderer: 'svg',
      });
    }
    this.#echartsDonut.setOption(options);
  }
}

if (!customElements.get('cms-world-map')) {
  customElements.define('cms-world-map', WorldMapElement);
}
