export default class FilesSelectedEvent extends CustomEvent {
  /**
   * Creates a new FilesSelectedEvent
   * @param files {FileList}
   */
  constructor({ files }) {
    super('filesSelected');
    this.files = files;
  }
}
