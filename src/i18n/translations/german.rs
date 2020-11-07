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

    map.insert("app.menu.my_jinya", "Mein Jinya");
    map.insert("app.menu.my_jinya.my_account", "Mein Account");
    map.insert("app.menu.my_jinya.my_account.my_profile", "Mein Profil");
    map.insert("app.menu.my_jinya.my_account.change_password", "Passwort ändern");
    map.insert("app.menu.my_jinya.my_account.logout", "Abmelden");

    map.insert("app.menu.configuration", "Konfiguration");
    map.insert("app.menu.configuration.generic", "Allgemein");
    map.insert("app.menu.configuration.generic.artists", "Künstler");
    map.insert("app.menu.configuration.frontend", "Frontend");
    map.insert("app.menu.configuration.frontend.menus", "Menüs");

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
    map.insert("segment_pages.designer.has_link", "Link?");
    map.insert("segment_pages.designer.action.script", "Skript");
    map.insert("segment_pages.designer.action.link", "Link");
    map.insert("segment_pages.designer.action.none", "Keine");
    map.insert("segment_pages.designer.target", "Ziel");
    map.insert("segment_pages.designer.script", "Skript");
    map.insert("segment_pages.designer.gallery", "Galerie");
    map.insert("segment_pages.designer.html", "Formatierter Text");
    map.insert("segment_pages.designer.file", "Datei");
    map.insert("segment_pages.designer.error_load_galleries", "Laden der Galerien fehlgeschlagen");
    map.insert("segment_pages.designer.error_load_files", "Laden der Dateien fehlgeschlagen");
    map.insert("segment_pages.designer.error_load_segments", "Laden der Segmente fehlgeschlagen");
    map.insert("segment_pages.designer.delete.approve", "Segment löschen");
    map.insert("segment_pages.designer.delete.decline", "Segment behalten");
    map.insert("segment_pages.designer.delete.title", "Segment wirklich löschen?");
    map.insert("segment_pages.designer.delete.content", "Bist du sicher, dass du das gewählte Segment löschen willst?");
    map.insert("segment_pages.designer.delete.failed", "Das Segment konnte nicht gelöscht werden.");
    map.insert("segment_pages.designer.action_save_segment", "Änderungen speichern");
    map.insert("segment_pages.designer.action_discard_segment", "Änderungen verwerfen");
    map.insert("segment_pages.designer.error_change_segment_failed", "Das Segment konnte nicht geändert werden");
    map.insert("segment_pages.designer.error_create_segment_failed", "Das Segment konnte nicht hinzugefügt werden");

    map.insert("my_jinya.my_account.change_password.title", "Passwort ändern");
    map.insert("my_jinya.my_account.change_password.old_password", "Bisheriges Passwort");
    map.insert("my_jinya.my_account.change_password.new_password", "Neues Passwort");
    map.insert("my_jinya.my_account.change_password.action_save", "Passwort ändern");
    map.insert("my_jinya.my_account.change_password.action_discard", "Passwort beibehalten");
    map.insert("my_jinya.my_account.change_password.saving", "Passwort wird geändert");
    map.insert("my_jinya.my_account.change_password.error_old_password_empty", "Bisheriges Passwort muss angegeben werden");
    map.insert("my_jinya.my_account.change_password.error_new_password_empty", "Neues Passwort muss angegeben werden");
    map.insert("my_jinya.my_account.change_password.error_forbidden", "Das alte Passwort ist falsch.");
    map.insert("my_jinya.my_account.change_password.error_generic", "Ein unbekannter Fehler ist aufgetreten.");

    map.insert("my_jinya.my_account.my_profile.save", "Profil speichern");
    map.insert("my_jinya.my_account.my_profile.discard", "Änderungen verwerfen");
    map.insert("my_jinya.my_account.my_profile.artist_name", "Künstlername");
    map.insert("my_jinya.my_account.my_profile.email", "Email");
    map.insert("my_jinya.my_account.my_profile.profile_picture", "Profilbild");
    map.insert("my_jinya.my_account.my_profile.error_artist_name_empty", "Der Künstlername ist erforderlich");
    map.insert("my_jinya.my_account.my_profile.error_email_empty", "Die Emailadresse ist erforderlich");
    map.insert("my_jinya.my_account.my_profile.error_profile_picture_failed", "Das Profilbild konnte nicht hochgeladen werden");
    map.insert("my_jinya.my_account.my_profile.error_profile_failed", "Die Änderungen am Profil konnten nicht gespeichert werden.");

    map.insert("artists.card.button.profile", "Profil");
    map.insert("artists.card.button.edit", "Künstler bearbeiten");
    map.insert("artists.card.button.delete", "Künstler löschen");
    map.insert("artists.card.button.deactivate", "Künstler deaktivieren");
    map.insert("artists.card.button.activate", "Künstler aktivieren");

    map.insert("artists.delete.approve", "Künstler löschen");
    map.insert("artists.delete.decline", "Künstler behalten");
    map.insert("artists.delete.title", "Künstler wirklich löschen?");
    map.insert("artists.delete.content", "Bist du sicher, dass du \"{artist_name}\" löschen willst? {artist_name} wird sich nicht mehr anmelden oder Inhalte erstellen können.");
    map.insert("artists.delete.failed", "\"{artist_name}\" konnte nicht gelöscht werden.");
    map.insert("artists.delete.success", "\"{artist_name}\" wurde gelöscht.");
    map.insert("artists.delete.failed_created_content", "\"{artist_name}\" konnte nicht gelöscht werden. {artist_name} hat Inhalte erstellt, du kannst {artist_name} aber deaktivieren.");

    map.insert("artists.deactivate.approve", "Künstler deaktivieren");
    map.insert("artists.deactivate.decline", "Künstler nicht deaktivieren");
    map.insert("artists.deactivate.title", "Künstler wirklich deaktivieren?");
    map.insert("artists.deactivate.content", "Bist du sicher, dass du \"{artist_name}\" deaktivieren willst? {artist_name} wird sich nicht mehr anmelden oder Inhalte erstellen können.");
    map.insert("artists.deactivate.success", "\"{artist_name}\" wurde deaktiviert.");
    map.insert("artists.deactivate.failed", "\"{artist_name}\" konnte nicht deaktiviert werden.");

    map.insert("artists.activate.approve", "Künstler aktivieren");
    map.insert("artists.activate.decline", "Künstler nicht aktivieren");
    map.insert("artists.activate.title", "Künstler wirklich aktivieren?");
    map.insert("artists.activate.content", "Bist du sicher, dass du \"{artist_name}\" aktivieren willst? {artist_name} wird sich danach anmelden oder Inhalte erstellen können.");
    map.insert("artists.activate.failed", "\"{artist_name}\" konnte nicht aktiviert werden.");
    map.insert("artists.activate.success", "\"{artist_name}\" wurde aktiviert.");

    map.insert("artists.profile.not_found", "Der Künstler wurde nicht gefunden.");

    map.insert("artists.add.title", "Künstler erstellen");
    map.insert("artists.add.artist_name", "Künstlername");
    map.insert("artists.add.email", "Email");
    map.insert("artists.add.password", "Passwort");
    map.insert("artists.add.profile_picture", "Profilbild");
    map.insert("artists.add.roles", "Rollen");
    map.insert("artists.add.action_save", "Künstler erstellen");
    map.insert("artists.add.action_discard", "Künstler verwerfen");
    map.insert("artists.add.error_artist_name_empty", "Künstlername ist erforderlich");
    map.insert("artists.add.error_password_empty", "Passwort ist erforderlich");
    map.insert("artists.add.error_email_empty", "Email ist erforderlich");
    map.insert("artists.add.error_generic", "Der Künstler konnte nicht angelegt werden");
    map.insert("artists.add.error_exists", "Ein Künstler mit der gleichen Emailadresse existiert bereits");
    map.insert("artists.add.profile_picture.error_generic", "Das Profilbild konnte nicht hochgeladen werden");

    map.insert("artists.edit.title", "Künstler bearbeiten");
    map.insert("artists.edit.artist_name", "Künstlername");
    map.insert("artists.edit.email", "Email");
    map.insert("artists.edit.password", "Passwort");
    map.insert("artists.edit.profile_picture", "Profilbild");
    map.insert("artists.edit.roles", "Rollen");
    map.insert("artists.edit.action_save", "Künstler speichern");
    map.insert("artists.edit.action_discard", "Änderungen verwerfen");
    map.insert("artists.edit.error_artist_name_empty", "Künstlername ist erforderlich");
    map.insert("artists.edit.error_password_empty", "Passwort ist erforderlich");
    map.insert("artists.edit.error_email_empty", "Email ist erforderlich");
    map.insert("artists.edit.error_generic", "Der Künstler konnte nicht geändert werden");
    map.insert("artists.edit.error_exists", "Ein Künstler mit der gleichen Emailadresse existiert bereits");
    map.insert("artists.edit.profile_picture.error_generic", "Das Profilbild konnte nicht hochgeladen werden");

    map.insert("menus.overview.action_new", "Neues Menü");
    map.insert("menus.overview.action_edit", "Menü bearbeiten");
    map.insert("menus.overview.action_designer", "Menudesigner");
    map.insert("menus.overview.action_delete", "Menü löschen");
    map.insert("menus.overview.table.name_column", "Name");
    map.insert("menus.overview.table.logo_column", "Logo");

    map.insert("menus.delete.approve", "Menü löschen");
    map.insert("menus.delete.decline", "Menü behalten");
    map.insert("menus.delete.title", "Menü wirklich löschen?");
    map.insert("menus.delete.content", "Bist du sicher, dass du das Menü \"{name}\" löschen möchtest? Das Menü wird aus allen Themes entfernt.");
    map.insert("menus.delete.failed", "Das Menü \"{name}\" konnte nicht gelöscht werden.");

    map.insert("menus.add.title", "Menü hinzufügen");
    map.insert("menus.add.name", "Name");
    map.insert("menus.add.logo", "Logo");
    map.insert("menus.add.error_name_empty", "Der Name ist erforderlich");
    map.insert("menus.add.action_discard", "Menü verwerfen");
    map.insert("menus.add.action_save", "Menü speichern");
    map.insert("menus.add.saving", "Das Menü wird gespeichert");
    map.insert("menus.add.saved", "Das Menü {name} wurde erfolgreich gespeichert");
    map.insert("menus.add.error_generic", "Das Menü konnte nicht gespeichert werden");
    map.insert("menus.add.error_conflict", "Das Menü konnte nicht gespeichert werden, weil ein anderes Menü mit dem Namen existiert");
    map.insert("menus.add.error_load_files", "Die Dateien konnten nicht geladen werden");

    map.insert("menus.edit.title", "Menü bearbeiten");
    map.insert("menus.edit.name", "Name");
    map.insert("menus.edit.logo", "Logo");
    map.insert("menus.edit.error_name_empty", "Der Name ist erforderlich");
    map.insert("menus.edit.action_discard", "Änderungen verwerfen");
    map.insert("menus.edit.action_save", "Menü speichern");
    map.insert("menus.edit.saving", "Das Menü wird gespeichert");
    map.insert("menus.edit.saved", "Das Menü {name} wurde erfolgreich gespeichert");
    map.insert("menus.edit.error_generic", "Das Menü konnte nicht gespeichert werden");
    map.insert("menus.edit.error_conflict", "Das Menü konnte nicht gespeichert werden, weil ein anderes Menü mit dem Namen existiert");
    map.insert("menus.edit.error_load_files", "Die Dateien konnten nicht geladen werden");

    map.insert("menus.designer.gallery", "Galerie");
    map.insert("menus.designer.page", "Seite");
    map.insert("menus.designer.segment_page", "Segment Seite");
    map.insert("menus.designer.group", "Gruppe");
    map.insert("menus.designer.link", "Link");
    map.insert("menus.designer.profile", "Künstlerprofil");
    map.insert("menus.designer.error_load_menu_items", "Fehler beim Laden der Menüeinträge");

    map
}