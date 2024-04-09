import { Alpine } from '../../../../lib/alpine.js';
import { getEnvironment } from '../../foundation/api/environment.js';

Alpine.data('environmentData', () => ({
  variables: {},
  async init() {
    this.variables = await getEnvironment();
  },
}));
