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
    map.insert("app.menu.content.media.galleries", "Galerien");

    map.insert("app.title.home_page", "Startseite");

    map.insert("files.card.button.information", "Mehr Informationen");
    map.insert("files.card.button.edit", "Datei bearbeiten");
    map.insert("files.card.button.delete", "Datei löschen");

    map.insert("files.details.created_by", "Erstellt von");
    map.insert("files.details.created_at", "Erstellt am");
    map.insert("files.details.last_updated_by", "Zu letzt geändert von");
    map.insert("files.details.last_updated_at", "Zu letzt geändert am");

    map.insert("files.delete.approve", "Datei löschen");
    map.insert("files.delete.decline", "Datei behalten");
    map.insert("files.delete.title", "Datei wirklich löschen?");
    map.insert("files.delete.content", "Bist du sicher, dass du die Datei \"{name}\" löschen möchtest? Die Datei wird aus allen Galerien und Themes entfernt.");
    map.insert("files.delete.failed", "Die Datei \"{name}\" konnte nicht gelöscht werden.");

    map.insert("files.edit.action_save", "Änderungen speichern");
    map.insert("files.edit.action_discard", "Änderungen verwerfen");
    map.insert("files.edit.name", "Name");
    map.insert("files.edit.file", "Datei");
    map.insert("files.edit.file_selected", "Datei ausgewählt");
    map.insert("files.edit.saved.success", "Datei {name} wurde erfolgreich gespeichert");
    map.insert("files.edit.error_generic", "Die Datei konnte nicht gespeichert werden");
    map.insert("files.edit.error_conflict", "Die Datei konnte nicht gespeichert werden, weil eine andere Datei mit dem Namen existiert");
    map.insert("files.edit.error_filename_empty", "Der Name muss angegeben werden");
    map.insert("files.edit.saving", "Die Datei wird gespeichert");
    map.insert("files.edit.title", "{name} bearbeiten");

    map.insert("files.add.action_save", "Datei speichern");
    map.insert("files.add.action_discard", "Datei verwerfen");
    map.insert("files.add.name", "Name");
    map.insert("files.add.file", "Datei");
    map.insert("files.add.select_file", "Datei auswählen");
    map.insert("files.add.saved.success", "Datei {name} wurde erfolgreich gespeichert");
    map.insert("files.add.error_generic", "Die Datei konnte nicht gespeichert werden");
    map.insert("files.add.error_conflict", "Die Datei konnte nicht gespeichert werden, weil eine andere Datei mit dem Namen existiert");
    map.insert("files.add.error_filename_empty", "Der Name muss angegeben werden");
    map.insert("files.add.error_file_missing", "Es muss eine Datei ausgewählt werden");
    map.insert("files.add.saving", "Die Datei wird gespeichert");
    map.insert("files.add.title", "Neue Datei");

    map.insert("galleries.overview.action_new", "Neue Galerie");
    map.insert("galleries.overview.action_edit", "Galerie bearbeiten");
    map.insert("galleries.overview.action_designer", "Galeriedesigner");
    map.insert("galleries.overview.action_delete", "Galerie löschen");
    map.insert("galleries.overview.table.name_column", "Name");
    map.insert("galleries.overview.table.orientation_column", "Orientierung");
    map.insert("galleries.overview.table.type_column", "Typ");
    map.insert("galleries.overview.table.description_column", "Beschreibung");
    map.insert("galleries.overview.table.orientation.horizontal", "Horizontal");
    map.insert("galleries.overview.table.orientation.vertical", "Vertikal");
    map.insert("galleries.overview.table.type.masonry", "Raster");
    map.insert("galleries.overview.table.type.sequence", "Liste");

    map.insert("galleries.delete.approve", "Galerie löschen");
    map.insert("galleries.delete.decline", "Galerie behalten");
    map.insert("galleries.delete.title", "Galerie wirklich löschen?");
    map.insert("galleries.delete.content", "Bist du sicher, dass du die Galerie \"{name}\" löschen möchtest? Die Datei wird aus allen Menüs und Themes entfernt.");
    map.insert("galleries.delete.failed", "Die Galerie \"{name}\" konnte nicht gelöscht werden.");

    map.insert("galleries.add.title", "Galerie hinzufügen");
    map.insert("galleries.add.name", "Name");
    map.insert("galleries.add.orientation", "Ausrichtung");
    map.insert("galleries.add.orientation.vertical", "Vertikal");
    map.insert("galleries.add.orientation.horizontal", "Horizontal");
    map.insert("galleries.add.type", "Typ");
    map.insert("galleries.add.type.sequence", "Liste");
    map.insert("galleries.add.type.masonry", "Raster");
    map.insert("galleries.add.description", "Beschreibung");
    map.insert("galleries.add.error_name_empty", "Der Name ist erforderlich");
    map.insert("galleries.add.action_discard", "Galerie verwerfen");
    map.insert("galleries.add.action_save", "Galerie speichern");
    map.insert("galleries.add.saving", "Die Galerie wird gespeichert");
    map.insert("galleries.add.saved", "Die Galerie {name} wurde erfolgreich gespeichert");
    map.insert("galleries.add.error_generic", "Die Galerie konnte nicht gespeichert werden");
    map.insert("galleries.add.error_conflict", "Die Galerie konnte nicht gespeichert werden, weil eine andere Galerie mit dem Namen existiert");

    map.insert("galleries.edit.title", "{name} bearbeiten");
    map.insert("galleries.edit.name", "Name");
    map.insert("galleries.edit.orientation", "Ausrichtung");
    map.insert("galleries.edit.orientation.vertical", "Vertikal");
    map.insert("galleries.edit.orientation.horizontal", "Horizontal");
    map.insert("galleries.edit.type", "Typ");
    map.insert("galleries.edit.type.sequence", "Liste");
    map.insert("galleries.edit.type.masonry", "Raster");
    map.insert("galleries.edit.description", "Beschreibung");
    map.insert("galleries.edit.error_name_empty", "Der Name ist erforderlich");
    map.insert("galleries.edit.action_discard", "Galerie verwerfen");
    map.insert("galleries.edit.action_save", "Galerie speichern");
    map.insert("galleries.edit.saving", "Die Galerie wird gespeichert");
    map.insert("galleries.edit.saved", "Die Galerie {name} wurde erfolgreich gespeichert");
    map.insert("galleries.edit.error_generic", "Die Galerie konnte nicht gespeichert werden");
    map.insert("galleries.edit.error_conflict", "Die Galerie konnte nicht gespeichert werden, weil eine andere Galerie mit dem Namen existiert");

    map.insert("galleries.designer.delete_error","Die Datei konnte nicht entfernt werden");

    map
}