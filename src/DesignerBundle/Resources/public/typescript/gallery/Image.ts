namespace Gallery {
    export class Image {
        private _source: string;

        get source(): string {
            return this._source;
        }

        set source(value: string) {
            this._source = value;
        }

        private _title: string;

        get title(): string {
            return this._title;
        }

        set title(value: string) {
            this._title = value;
        }

        private _description: string;

        get description(): string {
            return this._description;
        }

        set description(value: string) {
            this._description = value;
        }
    }
}