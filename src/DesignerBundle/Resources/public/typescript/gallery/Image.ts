namespace Gallery {
    export class Image {
        private _slug;

        get slug() {
            return this._slug;
        }

        set slug(value) {
            this._slug = value;
        }

        private _position;

        get position() {
            return this._position;
        }

        set position(value) {
            this._position = value;
        }

        private _id;

        get id() {
            return this._id;
        }

        set id(value) {
            this._id = value;
        }

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