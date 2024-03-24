export default class FilesSelectedEvent extends CustomEvent {
  /**
   * Creates a new FilesSelectedEvent
   * @param files {FileList}
   * @param tags {string[]}
   */
  constructor({
                files,
                tags,
              }) {
    super('filesSelected');
    this.files = files;
    this.tags = tags;
  }
}
