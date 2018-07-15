import Lockr from "lockr";
import JinyaRequest from "@/framework/Ajax/JinyaRequest";

export async function refreshMe() {
  if (Lockr.get('JinyaApiKey')) {
    const user = await JinyaRequest.get('/api/account');
    Lockr.set('JinyaUser', user);
    Lockr.set('JinyaUserRoles', user.roles);
  }
}

export function getRoles() {
  return Lockr.get('JinyaUserRoles');
}

export function getMe() {
  return Lockr.get('JinyaUser');
}