use std::collections::HashMap;

use anyhow::Error;
use yew::format::Json;
use yew::services::storage::Area;
use yew::services::StorageService;
use yew::utils::window;

use crate::i18n::translations::english::english_translations;
use crate::i18n::translations::german::german_translations;

pub struct Translator<'a> {
    german: HashMap<&'a str, &'a str>,
    english: HashMap<&'a str, &'a str>,
    browser_language: Language,
}

enum Language {
    De,
    En,
}

impl<'a> Translator<'a> {
    pub fn new() -> Self {
        let storage = StorageService::new(Area::Local).unwrap();
        let storage_lang = if let Json(Ok(data)) = storage.restore::<Json<Result<String, Error>>>("lang") {
            if data.to_lowercase().starts_with("de") {
                Some(Language::De)
            } else {
                Some(Language::En)
            }
        } else {
            None
        };
        let navigator_lang = if window().navigator().language().unwrap().to_lowercase().starts_with("de") {
            Language::De
        } else {
            Language::En
        };

        Translator {
            german: german_translations(),
            english: english_translations(),
            browser_language: if storage_lang.is_some() {
                storage_lang.unwrap()
            } else {
                navigator_lang
            },
        }
    }

    fn get_value(&self, key: &'a str) -> &'a str {
        let value = match self.browser_language {
            Language::De => {
                if self.german.contains_key(key) {
                    self.german.get(key)
                } else {
                    None
                }
            }
            _ => None,
        };

        if value.is_none() {
            if self.english.contains_key(key) {
                self.english.get(key).unwrap()
            } else {
                key
            }
        } else {
            value.unwrap()
        }
    }

    pub fn translate(&self, key: &'a str) -> String {
        self.get_value(key).to_string()
    }
    pub fn translate_with_args(&self, key: &'a str, args: HashMap<&str, &str>) -> String {
        let mut value = self.get_value(key).to_string();

        for arg in args {
            value = value.replace(format!("{}{}{}", "{", arg.0, "}").as_str(), arg.1);
        }

        value
    }
}

#[macro_export]
macro_rules! map (
    { $($key:expr => $value:expr),+ } => {
        {
            let mut m = ::std::collections::HashMap::new();
            $(
                m.insert($key, $value);
            )+
            m
        }
     };
);