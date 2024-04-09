import { Alpine } from '../../../../lib/alpine.js';
import { getVersion, performUpdate } from '../../foundation/api/updates.js';
import confirm from '../../foundation/ui/confirm.js';
import localize from '../../foundation/localize.js';

Alpine.data('updatesData', () => ({
  versionData: {
    openB: '<b>',
    closeB: '</b>',
    currentVersion: '',
    mostRecentVersion: '',
  },
  async init() {
    const version = await getVersion();
    this.versionData.currentVersion = version.currentVersion;
    this.versionData.mostRecentVersion = version.mostRecentVersion;
  },
  async openUpdate() {
    const confirmed = await confirm({
      title: localize({ key: 'maintenance.update.perform_update.title' }),
      message: localize({ key: 'maintenance.update.perform_update.message' }),
      declineLabel: localize({ key: 'maintenance.update.perform_update.decline' }),
      approveLabel: localize({ key: 'maintenance.update.perform_update.approve' }),
    });
    if (confirmed) {
      this.update.open = true;
      try {
        await performUpdate();
      } finally {
        window.location.reload();
      }
    }
  },
  update: {
    open: false,
  },
}));
