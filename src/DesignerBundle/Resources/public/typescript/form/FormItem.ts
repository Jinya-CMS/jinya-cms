class FormItem {
    private _required = ko.observable<boolean>();

    get required(): KnockoutObservable<boolean> {
        return this._required;
    }

    set required(value: KnockoutObservable<boolean>) {
        this._required = value;
    }

    private _selectOptions = ko.observable<string>();

    get selectOptions(): KnockoutObservable<string> {
        return this._selectOptions;
    }

    set selectOptions(value: KnockoutObservable<string>) {
        this._selectOptions = value;
    }

    private _helpText = ko.observable<string>();

    get helpText(): KnockoutObservable<string> {
        return this._helpText;
    }

    set helpText(value: KnockoutObservable<string>) {
        this._helpText = value;
    }

    private _options = ko.observable<object>();

    get options(): KnockoutObservable<object> {
        return this._options;
    }

    set options(value: KnockoutObservable<object>) {
        this._options = value;
    }

    private _type = ko.observable<string>();

    get type(): KnockoutObservable<string> {
        return this._type;
    }

    set type(value: KnockoutObservable<string>) {
        this._type = value;
    }

    private _label = ko.observable<string>();

    get label(): KnockoutObservable<string> {
        return this._label;
    }

    set label(value: KnockoutObservable<string>) {
        this._label = value;
    }
}