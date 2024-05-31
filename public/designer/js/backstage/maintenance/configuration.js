import { Alpine } from '../../../../lib/alpine.js';
import { getConfiguration, updateConfiguration } from '../../foundation/api/configuration.js';
import localize from '../../foundation/utils/localize.js';

Alpine.data('configurationData', () => ({
  configuration: {
    mysql: {
      database: '',
      host: '',
      password: '',
      port: 0,
      user: '',
    },
    mailer: {
      encryption: '',
      from: '',
      host: '',
      password: '',
      port: 0,
      smtp_auth: false,
      username: '',
    },
    jinya: {
      api_key_expiry: 86400,
      update_server: 'https://releases.jinya.de/cms',
    },
  },
  get mailerEncryption() {
    if (this.configuration.mailer.encryption === 'tls') {
      return 'STARTTLS';
    } else if (this.configuration.mailer.encryption === 'ssl') {
      return 'SMTPS';
    } else {
      return localize({ key: 'maintenance.configuration.mailer.no_encryption' });
    }
  },
  async init() {
    this.configuration = await getConfiguration();
  },
  async openUpdateDialog() {
    this.update.configuration = await getConfiguration();
    this.update.error.reset();
    this.update.open = true;
  },
  async updateConfiguration() {
    try {
      await updateConfiguration(this.update.configuration);
      this.configuration = await getConfiguration();
      this.update.open = false;
    } catch (e) {
      this.update.error.hasError = true;
      this.update.error.title = localize({ key: 'maintenance.configuration.update.error.title' });
      this.update.error.message = localize({ key: 'maintenance.configuration.update.error.message' });
    }
  },
  update: {
    open: false,
    configuration: {
      mysql: {
        database: '',
        host: '',
        password: '',
        port: 0,
        user: '',
      },
      mailer: {
        encryption: '',
        from: '',
        host: '',
        password: '',
        port: 0,
        smtp_auth: false,
        username: '',
      },
      jinya: {
        api_key_expiry: 86400,
        update_server: 'https://releases.jinya.de/cms',
      },
    },
    error: {
      hasError: false,
      title: '',
      message: '',
      reset() {
        this.hasError = false;
        this.title = '';
        this.message = '';
      },
    },
  },
}));
