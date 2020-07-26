#![recursion_limit = "10240"]

use wasm_bindgen::prelude::*;

mod app;
mod views;
mod storage;
mod ajax;
mod i18n;
mod models;
mod agents;

// When the `wee_alloc` feature is enabled, use `wee_alloc` as the global
// allocator.
#[cfg(feature = "wee_alloc")]
#[global_allocator]
static ALLOC: wee_alloc::WeeAlloc = wee_alloc::WeeAlloc::INIT;

// This is the entry point for the web app
#[wasm_bindgen]
pub fn run_app() -> Result<(), JsValue> {
    wasm_logger::init(wasm_logger::Config::default());
    jinya_ui::init();
    yew::start_app::<app::JinyaDesignerApp>();
    Ok(())
}
