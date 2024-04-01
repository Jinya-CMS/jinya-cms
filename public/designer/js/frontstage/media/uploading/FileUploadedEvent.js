export default class FileUploadedEvent extends CustomEvent {
  /**
   * Creates a new FileUploadedEvent
   * @param file {{id: number, path: string, name: string}}
   */
  constructor({ file }) {
    super('file-uploaded');
    this.file = file;
  }
}
