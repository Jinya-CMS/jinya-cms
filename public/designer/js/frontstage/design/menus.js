import { Alpine } from '../../../../lib/alpine.js';
import {
  createMenu,
  deleteMenu,
  getMenuItems,
  getMenus,
  updateMenu,
  updateMenuItems,
} from '../../foundation/api/menus.js';
import localize from '../../foundation/utils/localize.js';
import confirm from '../../foundation/ui/confirm.js';
import isEqual from '../../../lib/lodash/isEqual.js';
import filePicker from '../../foundation/ui/filePicker.js';
import alert from '../../foundation/ui/alert.js';
import { getGalleries } from '../../foundation/api/galleries.js';
import { getFiles } from '../../foundation/api/files.js';
import { getClassicPages } from '../../foundation/api/classic-pages.js';
import { getModernPages } from '../../foundation/api/modern-pages.js';
import { getBlogCategories } from '../../foundation/api/blog-categories.js';
import { getForms } from '../../foundation/api/forms.js';
import { getArtists } from '../../foundation/api/artists.js';
import { getMenuDatabase } from '../../foundation/database/menu.js';

const menuDatabase = getMenuDatabase();

Alpine.data('menusData', () => ({
  menus: [],
  items: [],
  galleries: [],
  files: [],
  classicPages: [],
  modernPages: [],
  blogCategories: [],
  forms: [],
  artists: [],
  selectedMenu: null,
  getItemTitle(item) {
    let type = 'group';
    if (item.artistId) {
      type = 'artist';
    } else if (item.classicPageId) {
      type = 'classic_page';
    } else if (item.modernPageId) {
      type = 'modern_page';
    } else if (item.formId) {
      type = 'form';
    } else if (item.galleryId) {
      type = 'gallery';
    } else if (item.blogCategoryId) {
      type = 'blog_category';
    } else if (item.blogHomePage) {
      type = 'blog_home_page';
    } else if (item.route) {
      type = 'external_link';
    }

    return localize({ key: `design.menus.designer.type_${type}` });
  },
  getItemLabel(item) {
    if (item.artistId) {
      return item.artistName;
    } else if (item.classicPageId) {
      return item.classicPageName;
    } else if (item.modernPageId) {
      return item.modernPageName;
    } else if (item.formId) {
      return item.formName;
    } else if (item.galleryId) {
      return item.galleryName;
    } else if (item.blogCategoryId) {
      return item.blogCategoryName;
    }

    return null;
  },
  getDropdownLabel(id, text) {
    return `#${id} ${text}`;
  },
  async saveMenuItems() {
    await menuDatabase.saveItems(this.selectedMenu.id, this.prepareItems(Alpine.raw(this.items), this.selectedMenu.id));
  },
  async clearMenuItems() {
    await menuDatabase.deleteItems(this.selectedMenu.id);
  },
  async getMenuItems(id) {
    return this.prepareItems(await menuDatabase.getItems(id));
  },
  prepareItems(items, menuId = null, nesting = 0) {
    return items
      .map((item) => {
        const res = {
          title: item.title,
          highlighted: item.highlighted,
          artistId: item.artistId ?? item.artist?.id,
          artistName: item.artistName ?? item.artist?.artistName,
          classicPageId: item.classicPageId ?? item.classicPage?.id,
          classicPageName: item.classicPageName ?? item.classicPage?.title,
          formId: item.formId ?? item.form?.id,
          formName: item.formName ?? item.form?.title,
          galleryId: item.galleryId ?? item.gallery?.id,
          galleryName: item.galleryName ?? item.gallery?.name,
          modernPageId: item.modernPageId ?? item.modernPage?.id,
          modernPageName: item.modernPageName ?? item.modernPage?.name,
          route: item.route,
          blogCategoryId: item.blogCategoryId ?? item.blogCategory?.id,
          blogCategoryName: item.blogCategoryName ?? item.blogCategory?.name,
          blogHomePage: item.blogHomePage,
          menuId: menuId ?? item.menuId ?? item.menu?.id,
          nesting: item.nesting ?? nesting,
        };

        if (item.items) {
          return [res, ...this.prepareItems(item.items, menuId, nesting + 1)];
        }

        return [res];
      })
      .flat(Infinity);
  },
  async init() {
    const menus = await getMenus();
    this.menus = menus.items;
    if (this.menus.length > 0) {
      await this.selectMenu(this.menus[0]);
    }

    const [galleries, files, classicPages, modernPages, blogCategories, forms, artists] = await Promise.all([
      getGalleries(),
      getFiles(),
      getClassicPages(),
      getModernPages(),
      getBlogCategories(),
      getForms(),
      getArtists(),
    ]);

    this.galleries = galleries.items;
    this.files = files.items;
    this.classicPages = classicPages.items;
    this.modernPages = modernPages.items;
    this.blogCategories = blogCategories.items;
    this.forms = forms.items;
    this.artists = artists.items;
  },
  async selectMenu(menu) {
    if (menu) {
      this.items = this.prepareItems(await getMenuItems(menu.id), menu.id);
    } else {
      this.items = [];
    }

    this.selectedMenu = menu;
    const savedMenuItems = await this.getMenuItems(menu.id);
    if (savedMenuItems.length === 0) {
      await this.saveMenuItems();
    } else if (savedMenuItems.length !== this.items.length || !isEqual(savedMenuItems, Alpine.raw(this.items))) {
      const confirmed = await confirm({
        title: localize({ key: 'design.menus.designer.load.title' }),
        message: localize({ key: 'design.menus.designer.load.message' }),
        declineLabel: localize({ key: 'design.menus.designer.load.decline' }),
        approveLabel: localize({ key: 'design.menus.designer.load.approve' }),
      });
      if (confirmed) {
        this.items = savedMenuItems;
      } else {
        await this.saveMenuItems();
      }
    }

    this.items = this.items.map((item) => ({
      ...item,
      _iid: crypto.randomUUID(),
    }));
  },
  openCreateDialog() {
    this.create.error.reset();
    this.create.logoId = null;
    this.create.name = '';
    this.create.picker.selected = localize({ key: 'design.menus.create.logo_none' });
    this.create.open = true;
  },
  openEditDialog() {
    this.edit.error.reset();
    this.edit.logoId = this.selectedMenu.logo?.id;
    this.edit.name = this.selectedMenu.name;
    this.create.picker.selected = this.selectedMenu.logo?.name ?? localize({ key: 'design.menus.create.logo_none' });
    this.edit.open = true;
  },
  openAddItemDialog(index, targetNesting) {
    this.addItem.index = index;
    this.addItem.targetNesting = targetNesting;
    this.addItem.open = true;
    this.addItem.title = '';
    this.addItem.route = '';
    this.addItem.type = 'type_group';
    this.addItem.highlighted = false;
    if (this.galleries.length > 0) {
      this.addItem.gallery = this.galleries[0].id;
    } else {
      this.addItem.gallery = undefined;
    }
    if (this.classicPages.length > 0) {
      this.addItem.classicPage = this.classicPages[0].id;
    } else {
      this.addItem.classicPage = undefined;
    }
    if (this.modernPages.length > 0) {
      this.addItem.modernPage = this.modernPages[0].id;
    } else {
      this.addItem.modernPage = undefined;
    }
    if (this.artists.length > 0) {
      this.addItem.artist = this.artists[0].id;
    } else {
      this.addItem.artist = undefined;
    }
    if (this.forms.length > 0) {
      this.addItem.form = this.forms[0].id;
    } else {
      this.addItem.form = undefined;
    }
    if (this.blogCategories.length > 0) {
      this.addItem.blogCategory = this.blogCategories[0].id;
    } else {
      this.addItem.blogCategory = undefined;
    }
    this.addItem.blogHomePage = undefined;
  },
  openEditItemDialog(item, index) {
    this.editItem.index = index;
    this.editItem.open = true;
    this.editItem.title = item.title;
    this.editItem.route = item.route;
    this.editItem.highlighted = item.highlighted;
    this.editItem.gallery = item.galleryId;
    this.editItem.classicPage = item.classicPageId;
    this.editItem.modernPage = item.modernPageId;
    this.editItem.artist = item.artistId;
    this.editItem.form = item.formId;
    this.editItem.blogCategory = item.blogCategoryId;
    this.editItem.blogHomePage = item.blogHomePage;

    if (this.editItem.gallery) {
      this.editItem.type = 'type_gallery';
    } else if (this.editItem.classicPage) {
      this.editItem.type = 'type_classic_page';
    } else if (this.editItem.modernPage) {
      this.editItem.type = 'type_modern_page';
    } else if (this.editItem.artist) {
      this.editItem.type = 'type_artist';
    } else if (this.editItem.form) {
      this.editItem.type = 'type_form';
    } else if (this.editItem.blogCategory) {
      this.editItem.type = 'type_blog_category';
    } else if (typeof this.editItem.route === 'string') {
      if (typeof this.editItem.blogHomePage === 'boolean') {
        this.editItem.type = 'type_blog_home_page';
      } else {
        this.editItem.type = 'type_external_link';
      }
    } else {
      this.editItem.type = 'type_group';
    }
  },
  async selectAddLogo() {
    const selectedFileId = this.create.logoId;
    const fileResult = await filePicker({
      title: localize({ key: 'design.menus.create.logo' }),
      selectedFileId,
    });
    if (fileResult) {
      this.create.logoId = fileResult.id;
      this.create.picker.selected = fileResult.name;
    }
  },
  async selectEditLogo() {
    const selectedFileId = this.edit.logoId;
    const fileResult = await filePicker({
      title: localize({ key: 'design.menus.edit.logo' }),
      selectedFileId,
    });
    if (fileResult) {
      this.edit.logoId = fileResult.id;
      this.edit.picker.selected = fileResult.name;
    }
  },
  async createMenu() {
    try {
      const menu = await createMenu(this.create.name, this.create.logoId);
      this.menus.push(menu);
      await this.selectMenu(menu);

      this.create.open = false;
    } catch (e) {
      if (e.status === 409) {
        this.create.error.message = localize({ key: 'design.menus.create.error.conflict' });
      } else {
        this.create.error.message = localize({ key: 'design.menus.create.error.generic' });
      }
      this.create.error.title = localize({ key: 'design.menus.create.error.title' });
      this.create.error.hasError = true;
    }
  },
  async updateMenu() {
    try {
      await updateMenu(this.selectedMenu.id, this.edit.name, this.edit.logoId);
      const menuIdx = this.menus.findIndex((menu) => this.selectedMenu.id === menu.id);

      this.menus[menuIdx].name = this.edit.name;
      this.menus[menuIdx].logo = {
        id: this.edit.logoId,
        name: this.edit.picker.name,
      };

      await this.selectMenu(this.menus[menuIdx]);

      this.edit.open = false;
    } catch (e) {
      if (e.status === 409) {
        this.edit.error.message = localize({ key: 'design.menus.edit.error.conflict' });
      } else {
        this.edit.error.message = localize({ key: 'design.menus.edit.error.generic' });
      }
      this.edit.error.title = localize({ key: 'design.menus.edit.error.title' });
      this.edit.error.hasError = true;
    }
  },
  async deleteMenu() {
    const confirmed = await confirm({
      title: localize({ key: 'design.menus.delete.title' }),
      message: localize({
        key: 'design.menus.delete.message',
        values: this.selectedMenu,
      }),
      approveLabel: localize({ key: 'design.menus.delete.delete' }),
      declineLabel: localize({ key: 'design.menus.delete.keep' }),
      negative: true,
    });
    if (confirmed) {
      try {
        await deleteMenu(this.selectedMenu.id);
        this.menus = this.menus.filter((menu) => menu.id !== this.selectedMenu.id);
        if (this.menus.length > 0) {
          await this.selectMenu(this.menus[0]);
        } else {
          await this.selectMenu(null);
        }
      } catch (e) {
        let message = '';
        if (e.status === 409) {
          message = localize({ key: 'design.menus.delete.error.conflict' });
        } else {
          message = localize({ key: 'design.menus.delete.error.generic' });
        }
        await alert({
          title: localize({ key: 'design.menus.delete.error.title' }),
          message,
          negative: true,
        });
      }
    }
  },
  mapItems(targetNesting, startIdx, endIdx) {
    const items = Alpine.raw(this.items);
    const res = [];
    for (let i = startIdx; i < endIdx; i++) {
      const item = items[i];
      if (targetNesting === item.nesting) {
        const endIdx = items.slice(i + 1, items.length)
          .findIndex((item) => item.nesting === targetNesting);
        const innerItems = this.mapItems(targetNesting + 1, i, endIdx === -1 ? items.length : endIdx + i + 1);
        res.push({
          title: item.title,
          highlighted: item.highlighted,
          artistId: item.artistId,
          classicPageId: item.classicPageId,
          formId: item.formId,
          galleryId: item.galleryId,
          modernPageId: item.modernPageId,
          route: item.route,
          blogCategoryId: item.blogCategoryId,
          blogHomePage: item.blogHomePage,
          items: innerItems,
        });
      }
    }

    return res;
  },
  async saveItems() {
    this.updateItems.message.reset();
    const items = this.mapItems(0, 0, this.items.length);

    try {
      await updateMenuItems(this.selectedMenu.id, items);

      this.updateItems.message.hasMessage = true;
      this.updateItems.message.isNegative = false;
      this.updateItems.message.title = localize({ key: 'design.menus.designer.success.title' });
      this.updateItems.message.content = localize({ key: 'design.menus.designer.success.message' });

      setTimeout(() => {
        this.updateItems.message.hasMessage = false;
      }, 30000);
    } catch (e) {
      this.updateItems.message.hasMessage = true;
      this.updateItems.message.isNegative = true;
      this.updateItems.message.title = localize({ key: 'design.menus.designer.error.title' });
      this.updateItems.message.content = localize({ key: 'design.menus.designer.error.message' });
    }
  },
  async deleteItem(item, index) {
    const confirmed = await confirm({
      title: localize({ key: 'design.menus.delete_item.title' }),
      message: localize({
        key: 'design.menus.delete_item.message',
        values: item,
      }),
      approveLabel: localize({ key: 'design.menus.delete_item.delete' }),
      declineLabel: localize({ key: 'design.menus.delete_item.keep' }),
      negative: true,
    });
    if (confirmed) {
      let count = 1;
      let prevNesting = item.nesting;
      for (let i = index + 1; i < this.items.length; i++) {
        if (this.items[i].nesting > prevNesting) {
          count++;
          prevNesting = this.items[i].nesting;
        } else {
          break;
        }
      }

      this.items.splice(index, count);

      await this.saveMenuItems();
    }
  },
  async insertItem() {
    const res = {
      title: this.addItem.title,
      highlighted: this.addItem.highlighted,
      menuId: this.selectedMenu.id,
      route: null,
      blogHomePage: null,
    };

    if (this.addItem.type !== 'type_group') {
      res.route = this.addItem.route;
    }

    if (this.addItem.type === 'type_artist') {
      const {
        id,
        artistName,
      } = this.artists.find((artist) => artist.id === this.addItem.artist);
      res.artistId = id;
      res.artistName = artistName;
    } else if (this.addItem.type === 'type_classic_page') {
      const {
        id,
        title,
      } = this.classicPages.find((classicPage) => classicPage.id === this.addItem.classicPage);
      res.classicPageId = id;
      res.classicPageName = title;
    } else if (this.addItem.type === 'type_modern_page') {
      const {
        id,
        name,
      } = this.modernPages.find((modernPage) => modernPage.id === this.addItem.modernPage);
      res.modernPageId = id;
      res.modernPageName = name;
    } else if (this.addItem.type === 'type_form') {
      const {
        id,
        title,
      } = this.forms.find((form) => form.id === this.addItem.form);
      res.formId = id;
      res.formName = title;
    } else if (this.addItem.type === 'type_gallery') {
      const {
        id,
        name,
      } = this.galleries.find((gallery) => gallery.id === this.addItem.gallery);
      res.galleryId = id;
      res.galleryName = name;
    } else if (this.addItem.type === 'type_blog_category') {
      const {
        id,
        name,
      } = this.blogCategories.find((blogCategory) => blogCategory.id === this.addItem.blogCategory);
      res.blogCategoryId = id;
      res.blogCategoryName = name;
    } else if (this.addItem.type === 'type_blog_home_page') {
      res.blogHomePage = this.addItem.blogHomePage;
    }

    if (this.addItem.index === this.items.length) {
      res.nesting = this.items[this.items.length - 1].nesting + 1;
    } else if (this.addItem.index === 0) {
      res.nesting = 0;
    } else {
      res.nesting = this.items[this.addItem.index - 1].nesting + 1;
    }

    this.items.splice(this.addItem.index, 0, res);
    this.addItem.open = false;
    await this.saveMenuItems();
  },
  async updateItem() {
    const res = {
      title: this.editItem.title,
      highlighted: this.editItem.highlighted,
      menuId: this.selectedMenu.id,
      route: null,
      blogHomePage: null,
      nesting: this.items[this.editItem.index].nesting,
    };

    if (this.editItem.type !== 'type_group') {
      res.route = this.editItem.route;
    }

    if (this.editItem.type === 'type_artist') {
      const {
        id,
        artistName,
      } = this.artists.find((artist) => artist.id === this.editItem.artist);
      res.artistId = id;
      res.artistName = artistName;
    } else if (this.editItem.type === 'type_classic_page') {
      const {
        id,
        title,
      } = this.classicPages.find((classicPage) => classicPage.id === this.editItem.classicPage);
      res.classicPageId = id;
      res.classicPageName = title;
    } else if (this.editItem.type === 'type_modern_page') {
      const {
        id,
        name,
      } = this.modernPages.find((modernPage) => modernPage.id === this.editItem.modernPage);
      res.modernPageId = id;
      res.modernPageName = name;
    } else if (this.editItem.type === 'type_form') {
      const {
        id,
        title,
      } = this.forms.find((form) => form.id === this.editItem.form);
      res.formId = id;
      res.formName = title;
    } else if (this.editItem.type === 'type_gallery') {
      const {
        id,
        name,
      } = this.galleries.find((gallery) => gallery.id === this.editItem.gallery);
      res.galleryId = id;
      res.galleryName = name;
    } else if (this.editItem.type === 'type_blog_category') {
      const {
        id,
        name,
      } = this.blogCategories.find((blogCategory) => blogCategory.id === this.editItem.blogCategory);
      res.blogCategoryId = id;
      res.blogCategoryName = name;
    } else if (this.editItem.type === 'type_blog_home_page') {
      res.blogHomePage = this.editItem.blogHomePage;
    }

    this.items.splice(this.editItem.index, 1, res);
    this.editItem.open = false;
    await this.saveMenuItems();
  },
  async increaseItemNesting(item, index) {
    let prevNesting = item.nesting;
    for (let i = index + 1; i < this.items.length; i++) {
      if (this.items[i].nesting > prevNesting) {
        prevNesting = this.items[i].nesting;
        this.items[i].nesting += 1;
      } else {
        break;
      }
    }

    this.items[index].nesting += 1;
    await this.saveMenuItems();
  },
  async decreaseItemNesting(item, index) {
    let count = 1;
    let prevNesting = item.nesting;
    let moveDown = false;
    for (let i = index + 1; i < this.items.length; i++) {
      if (this.items[i].nesting > prevNesting) {
        count++;
        prevNesting = this.items[i].nesting;
      } else if (this.items[i].nesting === item.nesting) {
        moveDown = true;
        break;
      }
    }

    let newPosition = index;
    if (moveDown) {
      newPosition = index + 1;
      const oldItems = this.items.splice(index, count);
      for (newPosition = index; newPosition < this.items.length; newPosition++) {
        if (this.items[newPosition]?.nesting === item.nesting - 1) {
          break;
        }
      }

      this.items.splice(newPosition, 0, ...oldItems);
    }

    prevNesting = item.nesting;
    for (let i = newPosition + 1; i < this.items.length; i++) {
      if (this.items[i].nesting > prevNesting) {
        prevNesting = this.items[i].nesting;
        this.items[i].nesting -= 1;
      } else {
        break;
      }
    }

    this.items[newPosition].nesting = item.nesting - 1;

    await this.saveMenuItems();
    console.log(this.items);
  },
  async startItemDrag(item, index) {
    this.moveItem.draggingItemIndex = index;
    this.moveItem.movingItems = [Alpine.raw(item)];
    for (let i = index + 1; i < this.items.length; ++i) {
      const child = this.items[i];
      if (item.nesting >= child.nesting) {
        break;
      }

      this.moveItem.movingItems.push(Alpine.raw(child));
    }
  },
  async dragOver(item, index) {
    console.log(index);
    if (this.moveItem.movingItems.filter((elem) => elem._iid === item._iid).length > 0) {
      return;
    }

    const items = this.items;
    let movingItems = Alpine.raw(this.moveItem.movingItems);
    const firstItem = movingItems[0];
    const currItemNesting = item.nesting;
    const firstItemOriginalNesting = firstItem.nesting;

    if (index === 0) {
      movingItems = movingItems.map((elem) => ({
        ...elem,
        nesting: elem.nesting - firstItemOriginalNesting,
      }));
    } else if (index > this.moveItem.draggingItemIndex) {
      movingItems = movingItems.map((elem) => ({
        ...elem,
        nesting: elem.nesting - firstItemOriginalNesting + currItemNesting + 1,
      }));
    } else if (index < this.moveItem.draggingItemIndex) {
      movingItems = movingItems.map((elem) => ({
        ...elem,
        nesting: elem.nesting - firstItemOriginalNesting + currItemNesting,
      }));
    }

    if (index > this.moveItem.draggingItemIndex) {
      items.splice(index + 1, 0, ...movingItems);
      items.splice(this.moveItem.draggingItemIndex, movingItems.length);
    } else if (index < this.moveItem.draggingItemIndex) {
      items.splice(this.moveItem.draggingItemIndex, movingItems.length);
      items.splice(index, 0, ...movingItems);
    }

    this.items = items;
    await this.saveMenuItems();
    this.moveItem.draggingItemIndex = index;
  },
  create: {
    open: false,
    name: '',
    logoId: null,
    get logoPath() {
      return `/image.php?id=${this.logoId}`;
    },
    picker: {
      label: localize({ key: 'design.menus.create.file_picker_label' }),
      selected: localize({ key: 'design.menus.create.logo_none' }),
    },
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
    name: '',
    logoId: null,
    get logoPath() {
      return `/image.php?id=${this.logoId}`;
    },
    picker: {
      label: localize({ key: 'design.menus.create.file_picker_label' }),
      selected: localize({ key: 'design.menus.create.logo_none' }),
      name: '',
    },
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
  addItem: {
    open: false,
    index: 0,
    type: 'type_group',
    title: '',
    route: '',
    highlighted: false,
    gallery: undefined,
    classicPage: undefined,
    modernPage: undefined,
    artist: undefined,
    form: undefined,
    blogCategory: undefined,
    blogHomePage: undefined,
  },
  editItem: {
    open: false,
    index: 0,
    type: 'type_group',
    title: '',
    route: '',
    gallery: undefined,
    classicPage: undefined,
    modernPage: undefined,
    artist: undefined,
    form: undefined,
    blogCategory: undefined,
    blogHomePage: undefined,
  },
  updateItems: {
    message: {
      reset() {
        this.hasMessage = false;
        this.title = '';
        this.content = '';
      },
      hasMessage: false,
      isNegative: false,
      title: '',
      content: '',
    },
  },
  moveItem: {
    draggingItemIndex: -1,
    movingItems: [],
  },
}));
