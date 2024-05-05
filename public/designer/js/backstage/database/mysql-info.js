import { Alpine } from '../../../../lib/alpine.js';
import { analyzeDatabase } from '../../foundation/api/mysql.js';

Alpine.data('mysqlInfoData', () => ({
  databaseInfo: {
    server: {},
    local: {},
    global: {},
    session: {},
  },
  selectedItem: 'server',
  async init() {
    this.databaseInfo = await analyzeDatabase();
  },
}));
