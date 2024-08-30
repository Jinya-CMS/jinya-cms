import '../../foundation/ui/components/diagrams/treemap-diagram.js';
import '../../foundation/ui/components/diagrams/bar-diagram.js';
import '../../foundation/ui/components/diagrams/line-diagram.js';
import '../../foundation/ui/components/diagrams/pie-diagram.js';
import { Alpine } from '../../../../lib/alpine.js';
import localize from '../../foundation/utils/localize.js';
import { getTotalVisitsForInterval } from '../../foundation/api/statistics.js';

Alpine.data('statisticsData', () => ({
  get range() {
    switch (this.$router.params?.interval) {
      case 'quarter':
        return 3;
      case 'half-year':
        return 6;
      case 'year':
        return 12;
      default:
        return 1;
    }
  },
  get title() {
    return localize({ key: `statistics.title.${this.$router.params?.interval?.replace('-', '_')}` });
  },
  totalVisits: '',
  async init() {
    const visits = await getTotalVisitsForInterval(this.range);

    this.totalVisits = localize({
      key: `statistics.access.total_visits`,
      values: { visits: visits.toLocaleString() },
    });
  },
}));
