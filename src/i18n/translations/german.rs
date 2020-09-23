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
    map.insert("app.menu.content.pages", "Seiten");
    map.insert("app.menu.content.pages.simple_pages", "Einfache Seiten");
    map.insert("app.menu.content.pages.simple_pages.edit", "Seite bearbeiten");
    map.insert("app.menu.content.pages.simple_pages.add", "Seite hinzufügen");
    map.insert("app.menu.content.pages.segment_pages", "Segment Seiten");

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
    map.insert("galleries.delete.content", "Bist du sicher, dass du die Galerie \"{name}\" löschen möchtest? Die Galerie wird aus allen Menüs und Themes entfernt.");
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

    map.insert("galleries.designer.error_delete", "Die Datei konnte nicht entfernt werden");
    map.insert("galleries.designer.error_add", "Die Datei konnte nicht hinzugefügt werden");
    map.insert("galleries.designer.error_update", "Die Datei konnte nicht verschoben werden");

    map.insert("simple_pages.overview.action_new", "Neue Seite");
    map.insert("simple_pages.overview.action_edit", "Seite bearbeiten");
    map.insert("simple_pages.overview.action_delete", "Seite löschen");
    map.insert("simple_pages.overview.table.title_column", "Titel");
    map.insert("simple_pages.overview.table.content_column", "Inhalt");

    map.insert("simple_pages.delete.approve", "Seite löschen");
    map.insert("simple_pages.delete.decline", "Seite behalten");
    map.insert("simple_pages.delete.title", "Seite wirklich löschen?");
    map.insert("simple_pages.delete.content", "Bist du sicher, dass du die Seite \"{title}\" löschen möchtest? Die Seite wird aus allen Menüs und Themes entfernt.");
    map.insert("simple_pages.delete.failed", "Die Seite \"{title}\" konnte nicht gelöscht werden.");

    map.insert("simple_pages.edit.title", "Titel");
    map.insert("simple_pages.edit.error_empty_title", "Der Titel ist erforderlich");
    map.insert("simple_pages.edit.update", "Seite speichern");
    map.insert("simple_pages.edit.error_conflict", "Die Seite konnte nicht gespeichert werden, weil eine andere Seite mit dem Namen existiert");
    map.insert("simple_pages.edit.error_generic", "Die Seite konnte nicht gespeichert werden");

    map.insert("simple_pages.add.title", "Titel");
    map.insert("simple_pages.add.error_empty_title", "Der Titel ist erforderlich");
    map.insert("simple_pages.add.create", "Seite speichern");
    map.insert("simple_pages.add.error_conflict", "Die Seite konnte nicht gespeichert werden, weil eine andere Seite mit dem Namen existiert");
    map.insert("simple_pages.add.error_generic", "Die Seite konnte nicht gespeichert werden");

    map.insert("segment_pages.overview.action_new", "Neue Seite");
    map.insert("segment_pages.overview.action_edit", "Seite bearbeiten");
    map.insert("segment_pages.overview.action_delete", "Seite löschen");
    map.insert("segment_pages.overview.action_designer", "Seitendesigner");
    map.insert("segment_pages.overview.table.name_column", "Name");
    map.insert("segment_pages.overview.table.segment_count_column", "Segmentanzahl");

    map.insert("segment_pages.add.title", "Seite hinzufügen");
    map.insert("segment_pages.add.name", "Name");
    map.insert("segment_pages.add.error_name_empty", "Der Name ist erforderlich");
    map.insert("segment_pages.add.action_discard", "Seite verwerfen");
    map.insert("segment_pages.add.action_save", "Seite speichern");
    map.insert("segment_pages.add.saving", "Die Seite wird gespeichert");
    map.insert("segment_pages.add.saved", "Die Seite {name} wurde erfolgreich gespeichert");
    map.insert("segment_pages.add.error_generic", "Die Seite konnte nicht gespeichert werden");
    map.insert("segment_pages.add.error_conflict", "Die Seite konnte nicht gespeichert werden, weil eine andere Seite mit dem Namen existiert");

    map.insert("segment_pages.edit.title", "{name} bearbeiten");
    map.insert("segment_pages.edit.name", "Name");
    map.insert("segment_pages.edit.error_name_empty", "Der Name ist erforderlich");
    map.insert("segment_pages.edit.action_discard", "Änderungen verwerfen");
    map.insert("segment_pages.edit.action_save", "Seite speichern");
    map.insert("segment_pages.edit.saving", "Die Seite wird gespeichert");
    map.insert("segment_pages.edit.saved", "Die Seite {name} wurde erfolgreich gespeichert");
    map.insert("segment_pages.edit.error_generic", "Die Seite konnte nicht gespeichert werden");
    map.insert("segment_pages.edit.error_conflict", "Die Seite konnte nicht gespeichert werden, weil eine andere Seite mit dem Namen existiert");

    map.insert("segment_pages.delete.approve", "Seite löschen");
    map.insert("segment_pages.delete.decline", "Seite behalten");
    map.insert("segment_pages.delete.title", "Seite wirklich löschen?");
    map.insert("segment_pages.delete.content", "Bist du sicher, dass du die Seite \"{name}\" löschen möchtest? Die Seite wird aus allen Menüs und Themes entfernt.");
    map.insert("segment_pages.delete.failed", "Die Seite \"{name}\" konnte nicht gelöscht werden.");

    map.insert("segment_pages.designer.action", "Aktion");
    map.insert("segment_pages.designer.action.script", "Skript");
    map.insert("segment_pages.designer.action.link", "Link");
    map.insert("segment_pages.designer.action.none", "Keine");
    map.insert("segment_pages.designer.target", "Ziel");
    map.insert("segment_pages.designer.script", "Skript");
    map.insert("segment_pages.designer.gallery", "Galerie");
    map.insert("segment_pages.designer.html", "Formatierter Text");
    map.insert("segment_pages.designer.error_load_galleries", "Laden der Galerien fehlgeschlagen");
    map.insert("segment_pages.designer.error_load_files", "Laden der Dateien fehlgeschlagen");
    map.insert("segment_pages.designer.error_load_segments", "Laden der Segmente fehlgeschlagen");
    map.insert("segment_pages.designer.tabs.files", "Dateien");
    map.insert("segment_pages.designer.tabs.galleries", "Galerien");
    map.insert("segment_pages.designer.tabs.other", "Weitere");
    map.insert("segment_pages.designer.tabs.other.formatted_text", "Formatierter Text");

    map
}