import { Alpine } from '../../../../lib/alpine.js';
import { analyzeDatabase } from '../../foundation/api/mysql.js';
import localize from '../../foundation/utils/localize.js';

Alpine.data('tablesData', () => ({
  selectedTable: '',
  selectedTab: 'details',
  tables: [],
  get table() {
    return this.tables[this.selectedTable];
  },
  get tableSize() {
    return `${this.table.size / 1024} KB`;
  },
  getEmptySaveConstraintValue(value) {
    return value ?? localize({ key: 'database.constraints.none' });
  },
  async init() {
    const analysis = await analyzeDatabase();

    this.tables = analysis.tables;
    [this.selectedTable] = Object.keys(this.tables);
  },
}));
