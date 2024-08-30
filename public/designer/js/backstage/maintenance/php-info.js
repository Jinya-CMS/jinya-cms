import { Alpine } from '../../../../lib/alpine.js';
import { getPhpInfo } from '../../foundation/api/php-info.js';

Alpine.data('phpInfoData', () => ({
  phpInfo: {
    extensions: {},
    about: {},
    apache: {},
  },
  selectedExtension: null,
  async init() {
    this.phpInfo = await getPhpInfo();
  },
}));
