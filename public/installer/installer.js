import { Alpine } from '../lib/alpine.js';

Alpine.data('data', () => ({
  mysql: {
    host: '',
    port: 3306,
    database: 'jinya',
    username: 'jinya',
    password: '',
  },
  mailing: {
    host: '',
    port: 587,
    username: '',
    password: '',
    encryption: 'tls',
    from: '',
    authRequired: true,
  },
  error: {
    message: '',
    hasError: false,
  },
  firstAdmin: {
    artistName: '',
    email: '',
    password: '',
  },
  state: 'configuration',
  loading: false,
  async saveConfiguration() {
    this.loading = true;
    const data = {
      mailing: this.mailing,
      mysql: this.mysql,
    };
    const result = await fetch('/api/install/configuration', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
    if (!result.ok || result.status !== 204) {
      this.error.hasError = true;
      this.error.message = await result.text();
    } else {
      this.state = 'database';
    }
    this.loading = false;
  },
  async createDatabase() {
    this.loading = true;
    const result = await fetch('/api/install/database', {
      method: 'POST',
    });
    if (!result.ok || result.status !== 204) {
      this.error.hasError = true;
      this.error.message = await result.text();
    } else {
      this.state = 'first-admin';
    }
    this.loading = false;
  },
  async createFirstAdmin() {
    this.loading = true;
    const data = this.firstAdmin;
    const result = await fetch('/api/install/admin', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
    if (!result.ok || result.status !== 204) {
      this.error.hasError = true;
      this.error.message = await result.text();
    } else {
      this.state = 'finish';
    }
    this.loading = false;
  },
}));

Alpine.start();
