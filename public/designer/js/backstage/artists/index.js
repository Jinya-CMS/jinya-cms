import { Alpine } from '../../../../lib/alpine.js';
import {
  createArtist,
  deleteArtist,
  disableArtist,
  enableArtist,
  getArtists,
  updateArtist,
} from '../../foundation/api/artists.js';
import confirm from '../../foundation/ui/confirm.js';
import localize from '../../foundation/utils/localize.js';
import alert from '../../foundation/ui/alert.js';

Alpine.data('artistsData', () => ({
  artists: [],
  selectedArtist: null,
  get isMe() {
    return this.selectedArtist?.email === Alpine.store('artist').email;
  },
  async init() {
    const artists = await getArtists();
    this.artists = artists.items;
    this.selectArtist(this.artists[0]);
  },
  selectArtist(artist) {
    this.selectedArtist = artist;
  },
  async deleteArtist() {
    const confirmation = await confirm({
      title: localize({ key: 'artists.delete.title' }),
      message: localize({
        key: 'artists.delete.message',
        values: this.selectedArtist,
      }),
      approveLabel: localize({ key: 'artists.delete.delete' }),
      declineLabel: localize({ key: 'artists.delete.keep' }),
      negative: true,
    });
    if (confirmation) {
      try {
        await deleteArtist(this.selectedArtist.id);
        this.artists = this.artists.filter((artist) => artist.id !== this.selectedArtist.id);
        if (this.artists.length > 0) {
          await this.selectArtist(this.artists[0]);
        } else {
          this.selectedArtist = null;
        }
      } catch (e) {
        await alert({
          title: localize({ key: 'artists.delete.error.title' }),
          message: localize({ key: 'artists.delete.error.message' }),
          negative: true,
        });
      }
    }
  },
  async enableArtist() {
    const confirmation = await confirm({
      title: localize({ key: 'artists.enable.title' }),
      message: localize({
        key: 'artists.enable.message',
        values: this.selectedArtist,
      }),
      approveLabel: localize({ key: 'artists.enable.delete' }),
      declineLabel: localize({ key: 'artists.enable.keep' }),
    });
    if (confirmation) {
      try {
        await enableArtist(this.selectedArtist.id);
        this.selectedArtist.enabled = true;
        this.artists[this.artists.findIndex((a) => a.id === this.selectedArtist.id)].enabled = true;
      } catch (e) {
        await alert({
          title: localize({ key: 'artists.enable.error.title' }),
          message: localize({ key: 'artists.enable.error.message' }),
          negative: true,
        });
      }
    }
  },
  async disableArtist() {
    const confirmation = await confirm({
      title: localize({ key: 'artists.disable.title' }),
      message: localize({
        key: 'artists.disable.message',
        values: this.selectedArtist,
      }),
      approveLabel: localize({ key: 'artists.disable.delete' }),
      declineLabel: localize({ key: 'artists.disable.keep' }),
      negative: true,
    });
    if (confirmation) {
      try {
        await disableArtist(this.selectedArtist.id);
        this.selectedArtist.enabled = false;
        this.artists[this.artists.findIndex((a) => a.id === this.selectedArtist.id)].enabled = false;
      } catch (e) {
        await alert({
          title: localize({ key: 'artists.disable.error.title' }),
          message: localize({ key: 'artists.disable.error.message' }),
          negative: true,
        });
      }
    }
  },
  async createArtist() {
    try {
      const artist = await createArtist(this.create.artistName, this.create.email, this.create.password, this.create.roles);
      this.artists.push(artist);
      this.selectArtist(artist);
      this.create.open = false;
    } catch (e) {
      let message = '';
      if (e.status === 409) {
        message = localize({ key: 'artists.create.error.conflict' });
      } else {
        message = localize({ key: 'artists.create.error.generic' });
      }
      this.create.error.title = localize({ key: 'artists.create.error.title' });
      this.create.error.message = message;
      this.create.error.hasError = true;
    }
  },
  async updateArtist() {
    try {
      await updateArtist(this.selectedArtist.id, this.edit.artistName, this.edit.email, this.edit.password, this.edit.roles);
      const artistIdx = this.artists.findIndex((a) => a.id === this.selectedArtist.id);
      this.artists[artistIdx].artistName = this.edit.artistName;
      this.artists[artistIdx].email = this.edit.email;
      this.artists[artistIdx].isReader = this.edit.isReader;
      this.artists[artistIdx].isWriter = this.edit.isWriter;
      this.artists[artistIdx].isAdmin = this.edit.isAdmin;
      this.edit.open = false;
    } catch (e) {
      let message = '';
      if (e.status === 409) {
        message = localize({ key: 'artists.edit.error.conflict' });
      } else {
        message = localize({ key: 'artists.edit.error.generic' });
      }
      this.edit.error.title = localize({ key: 'artists.edit.error.title' });
      this.edit.error.message = message;
      this.edit.error.hasError = true;
    }
  },
  openCreateDialog() {
    this.create.error.reset();
    this.create.artistName = '';
    this.create.email = '';
    this.create.password = '';
    this.create.roles = ['ROLE_READER', 'ROLE_WRITER'];
    this.create.open = true;
  },
  openEditDialog() {
    this.edit.error.reset();
    this.edit.artistName = this.selectedArtist.artistName;
    this.edit.email = this.selectedArtist.email;
    this.edit.password = '';
    this.edit.roles = this.selectedArtist.roles;
    this.edit.open = true;
  },
  create: {
    open: false,
    artistName: '',
    email: '',
    password: '',
    roles: ['ROLE_READER', 'ROLE_WRITER'],
    error: {
      reset() {
        this.hasError = false;
        this.title = '';
        this.message = '';
      },
      hasError: false,
      title: '',
      message: '',
    },
  },
  edit: {
    open: false,
    artistName: '',
    email: '',
    password: '',
    isAdmin: false,
    isWriter: true,
    isReader: true,
    error: {
      reset() {
        this.hasError = false;
        this.title = '';
        this.message = '';
      },
      hasError: false,
      title: '',
      message: '',
    },
  },
}));
