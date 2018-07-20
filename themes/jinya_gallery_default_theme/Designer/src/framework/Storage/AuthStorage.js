import Lockr from 'lockr';

const lockrVariables = {
  ApiKey: 'jinya/auth/apiKey',
  CurrentUser: 'jinya/auth/user/current',
  CurrentUserRoles: 'jinya/auth/user/current/roles',
  DeviceCode: 'jinya/auth/device/code',
};

export function getApiKey() {
  return Lockr.get(lockrVariables.ApiKey);
}

export function getCurrentUser() {
  return Lockr.get(lockrVariables.CurrentUser);
}

export function getCurrentUserRoles() {
  return Lockr.get(lockrVariables.CurrentUserRoles);
}

export function getDeviceCode() {
  return Lockr.get(lockrVariables.DeviceCode);
}

export function setApiKey(apiKey) {
  return Lockr.set(lockrVariables.ApiKey, apiKey);
}

export function setCurrentUser(user) {
  return Lockr.set(lockrVariables.CurrentUser, user);
}

export function setCurrentUserRoles(roles) {
  return Lockr.set(lockrVariables.CurrentUserRoles, roles);
}

export function setDeviceCode(code) {
  return Lockr.set(lockrVariables.DeviceCode, code);
}

export function clearAuth(clearDeviceCode = false) {
  Lockr.rm(lockrVariables.CurrentUserRoles);
  Lockr.rm(lockrVariables.CurrentUser);
  Lockr.rm(lockrVariables.ApiKey);
  if (clearDeviceCode) {
    Lockr.rm(lockrVariables.DeviceCode);
  }
}
