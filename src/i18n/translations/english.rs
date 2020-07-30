use std::collections::HashMap;

pub fn english_translations() -> HashMap<&'static str, &'static str> {
    let mut map = HashMap::new();
    map.insert("login.welcome", "Welcome to Jinya");
    map.insert("login.username", "Email address");
    map.insert("login.password", "Password");
    map.insert("login.credits", "Photo from ");
    map.insert("login.action_login", "Login");
    map.insert("login.action_two_factor", "Request second factor");
    map.insert("login.error_invalid_password", "Wrong email or password");

    map.insert("2fa.header", "Your two factor code");
    map.insert("2fa.code", "Two factor code");
    map.insert("2fa.credits", "Photo from ");
    map.insert("2fa.action_login", "Login");
    map.insert("2fa.error_code", "The code is invalid");

    map.insert("home.credits", "Photo from ");

    map.insert("app.menu.search", "Search…");
    map.insert("app.menu.content", "Content");
    map.insert("app.menu.content.media", "Media");
    map.insert("app.menu.content.media.files", "Files");

    map.insert("app.title.home_page", "Startpage");

    map.insert("files.card.button.information", "More info");
    map.insert("files.card.button.edit", "Edit file");
    map.insert("files.card.button.delete", "Delete file");
    map.insert("files.delete.approve", "Delete file");
    map.insert("files.delete.decline", "Keep file");
    map.insert("files.delete.title", "Really delete file?");
    map.insert("files.delete.content", "Are you sure, that you want to delete \"{}\". The file will be removed from all galleries and themes.");

    map
}