import Routes from "@/router/Routes";

const permissionMap = {};
const role_writer = 'ROLE_WRITER';
const role_admin = 'ROLE_ADMIN';
const role_super_admin = 'ROLE_SUPER_ADMIN';

permissionMap[Routes.Art.Artworks.SavedInJinya.Overview.name] = role_writer;

export default permissionMap;