import { Alpine } from '../../../../lib/alpine.js';
import {
  addFileToGallery,
  deleteFileFromGallery,
  getFiles, getFilesByGallery, getGalleries, getTags, moveFileInGallery,
} from '../../foundation/api/media.js';
import localize from '../../foundation/localize.js';

import '../../foundation/ui/components/tag.js';
import '../../foundation/ui/components/tag-popup.js';

Alpine.data('galleriesData', () => ({
  galleries: [],
  tags: [],
  files: [],
  filesInGallery: [],
  toolboxFiles: [],
  designerFiles: [],
  activeTags: new Set(),
  selectedGallery: null,
  designerSortable: null,
  toolboxSortable: null,
  loading: true,
  draggingOverDesigner: true,
  draggingOverToolbox: false,
  fromToolbox: null,
  draggedItem: null,
  dropPosition: null,
  dragPosition: null,
  prevDragOver: null,
  defaultTagName: localize({ key: 'media.files.action.show_all_tags' }),
  get selectedTypeAndOrientation() {
    return localize({
      key: `media.galleries.designer.title.${this.selectedGallery.orientation.toLowerCase()}_${this.selectedGallery.type.toLowerCase()}`,
    });
  },
  async init() {
    const [files, tags, galleries] = await Promise.all([getFiles(), getTags(), getGalleries()]);
    this.files = files.items;
    this.tags = tags.items;
    this.galleries = galleries.items;
    this.loading = false;
    if (this.galleries.length > 0) {
      await this.selectGallery(this.galleries[0]);
    }
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
    if (this.prevDragOver !== file.id && this.draggedItem) {
      this.draggingOverDesigner = true;
      if (this.fromToolbox) {
        this.fromToolbox = false;
      }

      const newPosition = this.designerFiles.findIndex((f) => f.id === file.id);
      this.rearrangeItemsInDesigner(newPosition);
      this.prevDragOver = file.id;
    }
  },
  async endDesignerDrag(file) {
    if (this.dragPosition !== null) {
      if (this.draggingOverDesigner) {
        const targetPosition = this.designerFiles.findIndex((f) => f.id === file.id);
        await moveFileInGallery(this.selectedGallery.id, file.position, targetPosition);
      } else if (this.draggingOverToolbox) {
        await deleteFileFromGallery(this.selectedGallery.id, file.position);
        this.designerFiles = this.designerFiles.filter((f) => f.id !== file.id);
        this.draggingOverToolbox = false;
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
        this.draggingOverToolbox = false;
      }

      this.resetPositions();
    }

    this.draggingOverToolbox = false;
    this.draggingOverDesigner = false;
  },
  rearrangeItemsInDesigner(newPosition) {
    if (this.dragPosition !== null) {
      this.designerFiles.splice(this.dragPosition, 1);
    }
    this.designerFiles.splice(newPosition, 0, this.draggedItem);
    this.dragPosition = newPosition;
  },
  toolboxDragOver() {
    this.draggingOverToolbox = true;
  },
  resetPositions() {
    this.designerFiles = this.designerFiles.map((file, idx) => ({
      ...file,
      position: idx,
    }));
  },
  setupSortable() {
//    this.toolboxSortable = Sortable.create(this.$refs.toolboxSortable, {
//      group: 'gallery',
//      sort: false,
//      onAdd: async (e) => {
//        e.item.classList.add('is--medium');
//        await deleteFileFromGallery(this.selectedGallery.id, e.oldIndex);
//
//        const toolboxFiles = Alpine.raw(this.toolboxFiles);
//        toolboxFiles.splice(e.oldIndex, 0, this.designerFiles.at(e.oldIndex).file);
//        this.sortableHack(toolboxFiles, this.toolboxSortable);
//
//        const designerFiles = Alpine.raw(this.designerFiles);
//        designerFiles.splice(e.oldIndex, 1);
//        this.sortableHack(designerFiles, this.designerSortable);
//      },
//    });
//    this.designerSortable = Sortable.create(this.$refs.designerSortable, {
//      group: 'gallery',
//      sort: true,
//      onAdd: async (e) => {
//        e.item.classList.remove('is--medium');
//        const position = e.newIndex;
//
//        const fileId = this.toolboxFiles[e.oldIndex].id;
//        const newPosition = await addFileToGallery(this.selectedGallery.id, position, fileId);
//
//        const toolboxFiles = Alpine.raw(this.toolboxFiles);
//        this.toolboxFiles.splice(e.oldIndex, 1);
//        this.sortableHack(toolboxFiles, this.toolboxSortable);
//
//        const designerFiles = Alpine.raw(this.designerFiles);
//        designerFiles.splice(e.newIndex, 0, newPosition);
//        this.sortableHack(designerFiles, this.designerSortable);
//      },
//      onUpdate: async (e) => {
//        const oldPosition = e.oldIndex;
//        const dropIdx = e.newIndex;
//        await moveFileInGallery(this.selectedGallery.id, oldPosition, dropIdx);
//        const designerFiles = Alpine.raw(this.designerFiles);
//        const movedFile = designerFiles.splice(e.oldIndex, 1)[0];
//        movedFile.position = dropIdx;
//        designerFiles.splice(e.newIndex, 0, movedFile);
//
//        // HACK update prevKeys to new sort order
//        const keys = [];
//        for (const file of designerFiles) {
//          keys.push(file.id);
//        }
//        // eslint-disable-next-line no-underscore-dangle
//        this.$refs.designerSortable._x_prevKeys = keys;
//      },
//    });
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
}));
