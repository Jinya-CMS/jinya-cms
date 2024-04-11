import { Alpine } from '../../../../lib/alpine.js';
import { executeQueries } from '../../foundation/api/mysql.js';
import alert from '../../foundation/ui/alert.js';

import '../../../lib/ace/ace.js';
import '../../../lib/ace/jinya.js';
import '../../../lib/ace/mysql.js';
import localize from '../../foundation/utils/localize.js';

Alpine.data('queryToolData', () => ({
  ace: null,
  selectedResult: null,
  results: [],
  init() {
    this.$nextTick(() => {
      const editor = window.ace.edit(this.$refs.editor);
      editor.setTheme('ace/theme/jinya');
      editor.session.setMode('ace/mode/mysql');
      editor.setFontSize('1rem');
      editor.setShowPrintMargin(false);
      this.editor = editor;
    });
  },
  shortenedStatement(statement) {
    return statement.substring(0, 24);
  },
  async executeQueries() {
    try {
      this.selectedResult = null;
      this.results = [];
      const queries = this.editor.getValue();
      this.results = await executeQueries(queries);
      if (this.results.length > 0) {
        [this.selectedResult] = this.results;
      }
    } catch (e) {
      await alert({
        title: localize({ key: 'database.query_tool.error.title' }),
        message: localize({ key: 'database.query_tool.error.message' }),
        negative: true,
      });
    }
  },
}));
