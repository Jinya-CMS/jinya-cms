import { Alpine } from '../../../../lib/alpine.js';
import {
  addFileToGallery,
  createGallery,
  deleteFileFromGallery,
  deleteGallery,
  getFilesByGallery,
  getGalleries,
  getGallery,
  moveFileInGallery,
  updateGallery,
} from '../../foundation/api/galleries.js';
import { getFiles, getTags } from '../../foundation/api/files.js';
import localize from '../../foundation/utils/localize.js';
import confirm from '../../foundation/ui/confirm.js';
import alert from '../../foundation/ui/alert.js';
import { getFileDatabase } from '../../foundation/database/file.js';

import '../../foundation/ui/components/tag.js';
import '../../foundation/ui/components/tag-popup.js';

const fileDatabase = getFileDatabase();

Alpine.data('galleriesData', () => ({
  galleries: [],
  tags: [],
  files: [],
  filesInGallery: [],
  toolboxFiles: [],
  designerFiles: [],
  activeTags: new Set(),
  selectedGallery: null,
  loading: true,
  draggingOverDesigner: true,
  draggingOverToolbox: false,
  fromToolbox: null,
  draggedItem: null,
  dragPosition: null,
  prevDragOver: null,
  get defaultTagName() {
    return localize({ key: 'media.files.action.show_all_tags' });
  },
  get title() {
    return `#${this.selectedGallery.id} ${this.selectedGallery.name}`;
  },
  get typeAndOrientation() {
    return localize({
      key: `media.galleries.designer.title.${this.selectedGallery.orientation.toLowerCase()}_${this.selectedGallery.type.toLowerCase()}`,
    });
  },
  async init() {
    fileDatabase.watchFiles().subscribe({
      next: (files) => {
        this.files = files;
      },
    });

    this.tags = await fileDatabase.getAllTags();

    const galleries = await getGalleries();
    this.galleries = galleries.items;
    this.loading = false;
    if (this.galleries.length > 0) {
      await this.selectGallery(this.galleries[0]);
    }

    Promise.all([getFiles(), getTags()]).then(([files, tags]) => {
      fileDatabase.replaceFiles(files.items);
      fileDatabase.replaceTags(tags.items);

      this.files = files.items;
      this.tags = tags.items;
    });
  },
  async selectGallery(gallery) {
    this.selectedGallery = gallery;
    this.filesInGallery = await getFilesByGallery(gallery.id);
    this.designerFiles = this.filesInGallery;
    this.clearTags();
  },
  startFromDesigner(file) {
    this.draggedItem = file;
    this.dragPosition = file.position ?? null;
    this.fromToolbox = false;
  },
  startFromToolbox(file) {
    this.fromToolbox = true;
    this.draggedItem = {
      file,
      id: -1,
    };
  },
  dragOver(file) {
    this.draggingOverDesigner = true;
    if (this.prevDragOver !== file.id && this.draggedItem) {
      const newPosition = this.designerFiles.findIndex((f) => f.id === file.id);
      this.rearrangeItemsInDesigner(newPosition);
      this.prevDragOver = file.id;
    }
  },
  async endDesignerDrag(file) {
    if (this.dragPosition !== null) {
      if (this.draggingOverToolbox) {
        await deleteFileFromGallery(this.selectedGallery.id, file.position);
        this.designerFiles = this.designerFiles.filter((f) => f.id !== file.id);
      } else if (this.draggingOverDesigner) {
        const targetPosition = this.designerFiles.findIndex((f) => f.id === file.id);
        await moveFileInGallery(this.selectedGallery.id, file.position, targetPosition);
      }

      this.dragPosition = null;
      this.draggedItem = null;
    }

    this.resetPositions();
    this.draggingOverToolbox = false;
    this.draggingOverDesigner = false;
  },
  async endToolboxDrag(file) {
    if (this.draggingOverDesigner) {
      if (this.draggedItem) {
        const targetPosition = this.designerFiles.findIndex((f) => f.id === -1);
        const newPosition = await addFileToGallery(this.selectedGallery.id, targetPosition, file.id);
        this.designerFiles.splice(targetPosition, 1, newPosition);
        this.toolboxFiles = this.toolboxFiles.filter((f) => f.id !== file.id);
        this.dragPosition = null;
        this.draggedItem = null;
      }

      this.resetPositions();
    }

    this.draggingOverToolbox = false;
    this.draggingOverDesigner = false;
  },
  async createGallery() {
    try {
      const newGallery = await createGallery(
        this.create.name,
        this.create.orientation,
        this.create.type,
        this.create.description,
      );
      this.galleries.push(newGallery);
      await this.selectGallery(newGallery);
      this.create.open = false;
    } catch (err) {
      this.create.error.hasError = true;
      this.create.error.title = localize({ key: 'media.galleries.create.error.title' });
      if (err.status === 409) {
        this.create.error.message = localize({ key: 'media.galleries.create.error.conflict' });
      } else {
        this.create.error.message = localize({ key: 'media.galleries.create.error.generic' });
      }
    }
  },
  async updateGallery() {
    try {
      await updateGallery(
        this.selectedGallery.id,
        this.edit.name,
        this.edit.orientation,
        this.edit.type,
        this.edit.description,
      );
      const savedGallery = await getGallery(this.selectedGallery.id);
      this.edit.open = false;
      this.galleries[this.galleries.indexOf(this.selectedGallery)] = savedGallery;
      await this.selectGallery(savedGallery);
    } catch (err) {
      this.edit.error.hasError = true;
      this.edit.error.title = localize({ key: 'media.galleries.edit.error.title' });
      if (err.status === 409) {
        this.edit.error.message = localize({ key: 'media.galleries.edit.error.conflict' });
      } else {
        this.edit.error.message = localize({ key: 'media.galleries.edit.error.generic' });
      }
    }
  },
  async deleteGallery() {
    const confirmation = await confirm({
      title: localize({ key: 'media.galleries.delete.title' }),
      message: localize({
        key: 'media.galleries.delete.message',
        values: this.selectedGallery,
      }),
      declineLabel: localize({ key: 'media.galleries.delete.keep' }),
      approveLabel: localize({ key: 'media.galleries.delete.delete' }),
      negative: true,
    });
    if (confirmation) {
      try {
        await deleteGallery(this.selectedGallery.id);
        this.galleries = this.galleries.filter((gallery) => gallery.id !== this.selectedGallery.id);
        if (this.galleries.length > 0) {
          await this.selectGallery(this.galleries[0]);
        } else {
          this.selectedGallery = null;
        }
      } catch (e) {
        let message = '';
        if (e.status === 409) {
          message = localize({ key: 'media.galleries.delete.error.conflict' });
        } else {
          message = localize({ key: 'media.galleries.delete.error.generic' });
        }
        await alert({
          title: localize({ key: 'media.galleries.delete.error.title' }),
          message,
          negative: true,
        });
      }
    }
  },
  rearrangeItemsInDesigner(newPosition) {
    if (this.dragPosition !== null) {
      this.designerFiles.splice(this.dragPosition, 1);
    }
    this.designerFiles.splice(newPosition, 0, this.draggedItem);
    this.dragPosition = newPosition;
  },
  toolboxDragEnter() {
    this.draggingOverToolbox = true;
    this.draggingOverDesigner = false;
  },
  designerDragEnter() {
    this.draggingOverToolbox = false;
    this.draggingOverDesigner = true;
  },
  resetPositions() {
    this.designerFiles = this.designerFiles.map((file, idx) => ({
      ...file,
      position: idx,
    }));
  },
  clearTags() {
    this.activeTags.clear();
    this.setToolboxFiles();
  },
  toggleTag(tag) {
    if (this.activeTags.has(tag.id)) {
      this.activeTags.delete(tag.id);
    } else {
      this.activeTags.add(tag.id);
    }
    this.setToolboxFiles();
  },
  setToolboxFiles() {
    const idsInGallery = this.filesInGallery.map((file) => file.file.id);
    let filteredFiles = this.files.filter((file) => !idsInGallery.includes(file.id));
    if (this.selectedGallery) {
      filteredFiles = filteredFiles.filter((file) => !this.filesInGallery.includes(file.id));
    }
    if (this.activeTags.size > 0) {
      filteredFiles = this.files.filter((file) => file.tags.filter((tag) => this.activeTags.has(tag.id)).length > 0);
    }

    this.toolboxFiles = filteredFiles;
  },
  openCreateDialog() {
    this.create.error.hasError = false;
    this.create.name = '';
    this.create.description = '';
    this.create.orientation = 'horizontal';
    this.create.type = 'masonry';
    this.create.open = true;
  },
  openEditDialog() {
    this.edit.error.hasError = false;
    this.edit.name = this.selectedGallery.name;
    this.edit.description = this.selectedGallery.description;
    this.edit.orientation = this.selectedGallery.orientation;
    this.edit.type = this.selectedGallery.type;
    this.edit.open = true;
  },
  create: {
    error: {
      hasError: false,
      title: '',
      message: '',
    },
    name: '',
    orientation: 'horizontal',
    type: 'masonry',
    description: '',
    open: false,
  },
  edit: {
    error: {
      hasError: false,
      title: '',
      message: '',
    },
    name: '',
    orientation: 'horizontal',
    type: 'masonry',
    description: '',
    open: false,
  },
}));
