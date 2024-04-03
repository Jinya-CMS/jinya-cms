export default class EnqueueFileEvent extends CustomEvent {
  /**
   * Creates a new FilesSelectedEvent
   * @param files {File[]}
   * @param tags {string[]}
   */
  constructor({ files, tags }) {
    super('enqueue-files');
    this.files = files;
    this.tags = tags;
  }
}
