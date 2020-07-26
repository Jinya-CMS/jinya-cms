use std::collections::HashMap;

pub fn german_translations() -> HashMap<&'static str, &'static str> {
    let mut map = HashMap::new();
    map.insert("login.welcome", "Wilkommen bei Jinya");
    map.insert("login.username", "Emailadresse");
    map.insert("login.password", "Passwort");
    map.insert("login.credits", "Foto von ");
    map.insert("login.action_login", "Anmelden");
    map.insert("login.action_two_factor", "Zweiten Faktor anfordern");
    map.insert("login.error_invalid_password", "Falsche Email oder falsches Passwort");

    map.insert("2fa.header", "Dein Zwei Faktor Code");
    map.insert("2fa.code", "Zwei Faktor Code");
    map.insert("2fa.credits", "Foto von ");
    map.insert("2fa.action_login", "Login");
    map.insert("2fa.error_code", "Der Code ist falsch");

    map.insert("home.credits", "Foto von ");

    map.insert("app.menu.search", "Suche…");
    map.insert("app.menu.content", "Inhalte");
    map.insert("app.menu.content.media", "Medien");
    map.insert("app.menu.content.media.files", "Dateien");

    map.insert("app.title.home_page", "Startseite");

    map.insert("files.card.button.information", "Mehr Informationen");
    map.insert("files.card.button.edit", "Datei bearbeiten");
    map.insert("files.card.button.delete", "Datei löschen");

    map
}