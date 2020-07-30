#[macro_use]
mod translator;
mod translations;

pub type Translator = translator::Translator<'static>;