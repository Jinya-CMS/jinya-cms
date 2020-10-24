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
    map.insert("app.menu.content.media.galleries", "Galleries");
    map.insert("app.menu.content.pages", "Pages");
    map.insert("app.menu.content.pages.simple_pages", "Simple Pages");
    map.insert("app.menu.content.pages.simple_pages.edit", "Edit page");
    map.insert("app.menu.content.pages.simple_pages.add", "Add page");
    map.insert("app.menu.content.pages.segment_pages", "Segment Pages");

    map.insert("app.menu.my_jinya", "My Jinya");
    map.insert("app.menu.my_jinya.my_account", "My account");
    map.insert("app.menu.my_jinya.my_account.my_profile", "My profile");
    map.insert("app.menu.my_jinya.my_account.change_password", "Change password");
    map.insert("app.menu.my_jinya.my_account.logout", "Logout");

    map.insert("app.menu.configuration", "Configuration");
    map.insert("app.menu.configuration.generic", "General");
    map.insert("app.menu.configuration.generic.artists", "Artists");
    map.insert("app.menu.configuration.frontend", "Frontend");
    map.insert("app.menu.configuration.frontend.menus", "Menus");

    map.insert("app.title.home_page", "Startpage");

    map.insert("files.card.button.information", "More info");
    map.insert("files.card.button.edit", "Edit file");
    map.insert("files.card.button.delete", "Delete file");

    map.insert("files.details.created_by", "Created by");
    map.insert("files.details.created_at", "Created at");
    map.insert("files.details.last_updated_by", "Last updated by");
    map.insert("files.details.last_updated_at", "Last updated at");

    map.insert("files.delete.approve", "Delete file");
    map.insert("files.delete.decline", "Keep file");
    map.insert("files.delete.title", "Really delete file?");
    map.insert("files.delete.content", "Are you sure, that you want to delete \"{name}\". The file will be removed from all galleries and themes.");
    map.insert("files.delete.failed", "The file \"{name}\" could not be deleted.");

    map.insert("files.edit.action_save", "Save changes");
    map.insert("files.edit.action_discard", "Discard changes");
    map.insert("files.edit.name", "Name");
    map.insert("files.edit.file", "File");
    map.insert("files.edit.file_selected", "File selected");
    map.insert("files.edit.saved.success", "Successfully saved {name}");
    map.insert("files.edit.error_generic", "The file could not be saved");
    map.insert("files.edit.error_conflict", "The file could not be saved, because there is a file with the same name");
    map.insert("files.edit.error_filename_empty", "The name must be not empty");
    map.insert("files.edit.saving", "Saving the file");
    map.insert("files.edit.title", "Update {name}");

    map.insert("files.add.action_save", "Save file");
    map.insert("files.add.action_discard", "Discard file");
    map.insert("files.add.name", "Name");
    map.insert("files.add.file", "File");
    map.insert("files.add.select_file", "Select file");
    map.insert("files.add.saved.success", "Successfully saved {name}");
    map.insert("files.add.error_generic", "The file could not be saved");
    map.insert("files.add.error_conflict", "The file could not be saved, because there is a file with the same name");
    map.insert("files.add.error_filename_empty", "The name must be not empty");
    map.insert("files.add.error_file_missing", "You have to select a file");
    map.insert("files.add.saving", "Saving the file");
    map.insert("files.add.title", "New file}");

    map.insert("galleries.overview.action_new", "New gallery");
    map.insert("galleries.overview.action_edit", "Edit gallery");
    map.insert("galleries.overview.action_designer", "Gallery designer");
    map.insert("galleries.overview.action_delete", "Delete gallery");
    map.insert("galleries.overview.table.name_column", "Name");
    map.insert("galleries.overview.table.orientation_column", "Orientation");
    map.insert("galleries.overview.table.type_column", "Type");
    map.insert("galleries.overview.table.description_column", "Description");
    map.insert("galleries.overview.table.orientation.horizontal", "Horizontal");
    map.insert("galleries.overview.table.orientation.vertical", "Vertical");
    map.insert("galleries.overview.table.type.masonry", "Masonry");
    map.insert("galleries.overview.table.type.sequence", "Sequence");

    map.insert("galleries.delete.approve", "Delete gallery");
    map.insert("galleries.delete.decline", "Keep gallery");
    map.insert("galleries.delete.title", "Really delete gallery?");
    map.insert("galleries.delete.content", "Are you sure, that you want to delete \"{name}\". The gallery will be removed from all menus and themes.");
    map.insert("galleries.delete.failed", "The gallery \"{name}\" could not be deleted.");

    map.insert("galleries.add.title", "Add gallery");
    map.insert("galleries.add.name", "Name");
    map.insert("galleries.add.orientation", "Orientation");
    map.insert("galleries.add.orientation.vertical", "Vertical");
    map.insert("galleries.add.orientation.horizontal", "Horizontal");
    map.insert("galleries.add.type", "Type");
    map.insert("galleries.add.type.sequence", "Sequence");
    map.insert("galleries.add.type.masonry", "Masonry");
    map.insert("galleries.add.description", "Description");
    map.insert("galleries.add.error_name_empty", "The name is required");
    map.insert("galleries.add.action_discard", "Discard gallery");
    map.insert("galleries.add.action_save", "Save gallery");
    map.insert("galleries.add.saving", "Saving the gallery");
    map.insert("galleries.add.saved", "The gallery {name} was saved successfully.");
    map.insert("galleries.add.error_conflict", "The gallery could not be saved, because there is a gallery with the same name");
    map.insert("galleries.add.error_generic", "The gallery could not be saved");

    map.insert("galleries.edit.title", "Edit {name}");
    map.insert("galleries.edit.name", "Name");
    map.insert("galleries.edit.orientation", "Orientation");
    map.insert("galleries.edit.orientation.vertical", "Vertical");
    map.insert("galleries.edit.orientation.horizontal", "Horizontal");
    map.insert("galleries.edit.type", "Type");
    map.insert("galleries.edit.type.sequence", "Sequence");
    map.insert("galleries.edit.type.masonry", "Masonry");
    map.insert("galleries.edit.description", "Description");
    map.insert("galleries.edit.error_name_empty", "The name is required");
    map.insert("galleries.edit.action_discard", "Discard gallery");
    map.insert("galleries.edit.action_save", "Save gallery");
    map.insert("galleries.edit.saving", "Saving the gallery");
    map.insert("galleries.edit.saved", "The gallery {name} was saved successfully.");
    map.insert("galleries.edit.error_conflict", "The gallery could not be saved, because there is a gallery with the same name");
    map.insert("galleries.edit.error_generic", "The gallery could not be saved");

    map.insert("galleries.designer.error_delete", "The file could not be removed");
    map.insert("galleries.designer.error_add", "The file could not be added");
    map.insert("galleries.designer.error_update", "The file could not be moved");

    map.insert("simple_pages.overview.action_new", "New page");
    map.insert("simple_pages.overview.action_edit", "Edit page");
    map.insert("simple_pages.overview.action_delete", "Delete page");
    map.insert("simple_pages.overview.table.title_column", "Title");
    map.insert("simple_pages.overview.table.content_column", "Content");

    map.insert("simple_pages.delete.approve", "Delete page");
    map.insert("simple_pages.delete.decline", "Keep page");
    map.insert("simple_pages.delete.title", "Really delete page?");
    map.insert("simple_pages.delete.content", "Are you sure, that you want to delete \"{title}\". The page will be removed from all menus and themes.");
    map.insert("simple_pages.delete.failed", "The page \"{title}\" could not be deleted.");

    map.insert("simple_pages.edit.title", "Title");
    map.insert("simple_pages.edit.error_empty_title", "The title is required");
    map.insert("simple_pages.edit.update", "Save page");
    map.insert("simple_pages.edit.error_conflict", "The page could not be saved, because there is a page with the same name");
    map.insert("simple_pages.edit.error_generic", "The page could not be saved");

    map.insert("simple_pages.add.title", "Title");
    map.insert("simple_pages.add.error_empty_title", "The title is required");
    map.insert("simple_pages.add.create", "Save page");
    map.insert("simple_pages.add.error_conflict", "The page could not be saved, because there is a page with the same name");
    map.insert("simple_pages.add.error_generic", "The page could not be saved");

    map.insert("segment_pages.overview.action_new", "New page");
    map.insert("segment_pages.overview.action_edit", "Edit page");
    map.insert("segment_pages.overview.action_designer", "Page designer");
    map.insert("segment_pages.overview.action_delete", "Delete page");
    map.insert("segment_pages.overview.table.name_column", "Name");
    map.insert("segment_pages.overview.table.segment_count_column", "Segment count");

    map.insert("segment_pages.add.title", "Add page");
    map.insert("segment_pages.add.name", "Name");
    map.insert("segment_pages.add.error_name_empty", "The name is required");
    map.insert("segment_pages.add.action_discard", "Discard page");
    map.insert("segment_pages.add.action_save", "Save page");
    map.insert("segment_pages.add.saving", "Saving the page");
    map.insert("segment_pages.add.saved", "The page {name} was saved successfully.");
    map.insert("segment_pages.add.error_conflict", "The page could not be saved, because there is a page with the same name");
    map.insert("segment_pages.add.error_generic", "The page could not be saved");

    map.insert("segment_pages.edit.title", "Edit {name}");
    map.insert("segment_pages.edit.name", "Name");
    map.insert("segment_pages.edit.error_name_empty", "The name is required");
    map.insert("segment_pages.edit.action_discard", "Discard changes");
    map.insert("segment_pages.edit.action_save", "Save page");
    map.insert("segment_pages.edit.saving", "Saving the page");
    map.insert("segment_pages.edit.saved", "The page {name} was saved successfully.");
    map.insert("segment_pages.edit.error_conflict", "The page could not be saved, because there is a page with the same name");
    map.insert("segment_pages.edit.error_generic", "The page could not be saved");

    map.insert("segment_pages.delete.approve", "Delete page");
    map.insert("segment_pages.delete.decline", "Keep page");
    map.insert("segment_pages.delete.title", "Really delete page?");
    map.insert("segment_pages.delete.content", "Are you sure, that you want to delete \"{name}\". The page will be removed from all menus and themes.");
    map.insert("segment_pages.delete.failed", "The page \"{name}\" could not be deleted.");

    map.insert("segment_pages.designer.action", "Action");
    map.insert("segment_pages.designer.action.script", "Script");
    map.insert("segment_pages.designer.action.link", "Link");
    map.insert("segment_pages.designer.action.none", "None");
    map.insert("segment_pages.designer.target", "Target");
    map.insert("segment_pages.designer.script", "Script");
    map.insert("segment_pages.designer.gallery", "Gallery");
    map.insert("segment_pages.designer.html", "Formatted text");
    map.insert("segment_pages.designer.file", "File");
    map.insert("segment_pages.designer.error_load_galleries", "Failed to load the galleries");
    map.insert("segment_pages.designer.error_load_files", "Failed to load the files");
    map.insert("segment_pages.designer.error_load_segments", "Failed to load the segments");
    map.insert("segment_pages.designer.delete.approve", "Delete segment");
    map.insert("segment_pages.designer.delete.decline", "Keep segment");
    map.insert("segment_pages.designer.delete.title", "Really delete segment?");
    map.insert("segment_pages.designer.delete.content", "Are you sure, that you want to delete the selected segment?");
    map.insert("segment_pages.designer.delete.failed", "The segment could not be deleted.");
    map.insert("segment_pages.designer.action_save_segment", "Save changes");
    map.insert("segment_pages.designer.action_discard_segment", "Discard changes");
    map.insert("segment_pages.designer.error_change_segment_failed", "The segment could not be changed");
    map.insert("segment_pages.designer.error_create_segment_failed", "The segment could not be created");

    map.insert("my_jinya.my_account.change_password.title", "Change password");
    map.insert("my_jinya.my_account.change_password.old_password", "Current password");
    map.insert("my_jinya.my_account.change_password.new_password", "New password");
    map.insert("my_jinya.my_account.change_password.action_save", "Change password");
    map.insert("my_jinya.my_account.change_password.action_discard", "Keep password");
    map.insert("my_jinya.my_account.change_password.saving", "Changing password");
    map.insert("my_jinya.my_account.change_password.error_old_password_empty", "Current password is required");
    map.insert("my_jinya.my_account.change_password.error_new_password_empty", "New password is required");
    map.insert("my_jinya.my_account.change_password.error_forbidden", "The current password is wrong.");
    map.insert("my_jinya.my_account.change_password.error_generic", "An unknown error occurred.");

    map.insert("my_jinya.my_account.my_profile.save", "Save profile");
    map.insert("my_jinya.my_account.my_profile.discard", "Discard changes");
    map.insert("my_jinya.my_account.my_profile.artist_name", "Artist name");
    map.insert("my_jinya.my_account.my_profile.email", "Email");
    map.insert("my_jinya.my_account.my_profile.profile_picture", "Profile picture");
    map.insert("my_jinya.my_account.my_profile.error_artist_name_empty", "Artist name is required");
    map.insert("my_jinya.my_account.my_profile.error_email_empty", "Email is required");
    map.insert("my_jinya.my_account.my_profile.error_profile_picture_failed", "Failed to upload profile picture");
    map.insert("my_jinya.my_account.my_profile.error_profile_failed", "Failed to update profile data");

    map.insert("artists.card.button.profile", "Profile");
    map.insert("artists.card.button.edit", "Edit artist");
    map.insert("artists.card.button.delete", "Delete artist");
    map.insert("artists.card.button.deactivate", "Deactivate artist");
    map.insert("artists.card.button.activate", "Activate artist");

    map.insert("artists.delete.approve", "Delete artist");
    map.insert("artists.delete.decline", "Keep artist");
    map.insert("artists.delete.title", "Really delete artist?");
    map.insert("artists.delete.content", "Are you sure, that you want to delete \"{artist_name}\". The artist won't be able to login or create content.");
    map.insert("artists.delete.failed", "The artist \"{artist_name}\" could not be deleted.");
    map.insert("artists.delete.failed_created_content", "The artist \"{artist_name}\" could not be deleted. The artist created content, you can still deactivate the artist.");
    map.insert("artists.delete.success", "The artist \"{artist_name}\" was deleted.");

    map.insert("artists.deactivate.approve", "Deactivate artist");
    map.insert("artists.deactivate.decline", "Don't deactivate artist");
    map.insert("artists.deactivate.title", "Really deactivate artist?");
    map.insert("artists.deactivate.content", "Are you sure, that you want to deactivate \"{artist_name}\". The artist won't be able to login or create content.");
    map.insert("artists.deactivate.failed", "The artist \"{artist_name}\" could not be deactivated.");
    map.insert("artists.deactivate.success", "The artist \"{artist_name}\" was deactivated.");

    map.insert("artists.activate.approve", "Activate artist");
    map.insert("artists.activate.decline", "Don't activate artist");
    map.insert("artists.activate.title", "Really activate artist?");
    map.insert("artists.activate.content", "Are you sure, that you want to activate \"{artist_name}\". The artist will be able to login or create content.");
    map.insert("artists.activate.failed", "The artist \"{artist_name}\" could not be deactivated.");
    map.insert("artists.activate.success", "The artist \"{artist_name}\" was activated.");

    map.insert("artists.profile.not_found", "The artist was not found.");

    map.insert("artists.add.title", "Create artist");
    map.insert("artists.add.artist_name", "Artist name");
    map.insert("artists.add.email", "Email");
    map.insert("artists.add.password", "Password");
    map.insert("artists.add.profile_picture", "Profile picture");
    map.insert("artists.add.roles", "Roles");
    map.insert("artists.add.action_save", "Create artist");
    map.insert("artists.add.action_discard", "Discard artist");
    map.insert("artists.add.error_artist_name_empty", "The artist name is required");
    map.insert("artists.add.error_password_empty", "The password is required");
    map.insert("artists.add.error_email_empty", "The email is required");
    map.insert("artists.add.error_generic", "The artist could not be created");
    map.insert("artists.add.error_exists", "An artist with the given email address already exists");
    map.insert("artists.add.profile_picture.error_generic", "The profile picture could not be uploaded");

    map.insert("artists.edit.title", "Edit artist");
    map.insert("artists.edit.artist_name", "Artist name");
    map.insert("artists.edit.email", "Email");
    map.insert("artists.edit.password", "Password");
    map.insert("artists.edit.profile_picture", "Profile picture");
    map.insert("artists.edit.roles", "Roles");
    map.insert("artists.edit.action_save", "Save artist");
    map.insert("artists.edit.action_discard", "Discard changes");
    map.insert("artists.edit.error_artist_name_empty", "The artist name is required");
    map.insert("artists.edit.error_password_empty", "The password is required");
    map.insert("artists.edit.error_email_empty", "The email is required");
    map.insert("artists.edit.error_generic", "The artist could not be updated");
    map.insert("artists.edit.error_exists", "An artist with the given email address already exists");
    map.insert("artists.edit.profile_picture.error_generic", "The profile picture could not be uploaded");

    map.insert("menus.overview.action_new", "New menu");
    map.insert("menus.overview.action_edit", "Edit menu");
    map.insert("menus.overview.action_delete", "Delete menu");
    map.insert("menus.overview.table.name_column", "Name");
    map.insert("menus.overview.table.logo_column", "Logo");

    map.insert("menus.delete.approve", "Delete menu");
    map.insert("menus.delete.decline", "Keep menu");
    map.insert("menus.delete.title", "Really delete menu?");
    map.insert("menus.delete.content", "Are you sure, that you want to delete \"{name}\". The menu will be removed from all themes.");
    map.insert("menus.delete.failed", "The menu \"{name}\" could not be deleted.");

    map
}