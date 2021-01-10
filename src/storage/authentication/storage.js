export function hasJinyaApiKey() {
  return !!localStorage.getItem('/jinya/api/key');
}

export function getJinyaApiKey() {
  return localStorage.getItem('/jinya/api/key');
}

export function setJinyaApiKey(code) {
  localStorage.setItem('/jinya/api/key', code);
}

export function hasDeviceCode() {
  return !!localStorage.getItem('/jinya/device/code');
}

export function getDeviceCode() {
  return localStorage.getItem('/jinya/device/code');
}

export function deleteDeviceCode() {
  return localStorage.removeItem('/jinya/device/code');
}

export function deleteJinyaApiKey() {
  localStorage.removeItem('/jinya/api/key');
}

export function deleteRoles() {
  localStorage.removeItem('/jinya/auth/roles');
}

export function getRoles() {
  return JSON.parse(localStorage.getItem('/jinya/auth/roles'));
}

export function setDeviceCode(code) {
  localStorage.setItem('/jinya/device/code', code);
}

export function setEmail(email) {
  sessionStorage.setItem('/jinya/auth/email', email);
}

export function setPassword(password) {
  sessionStorage.setItem('/jinya/auth/password', password);
}

export function setRoles(roles) {
  localStorage.setItem('/jinya/auth/roles', JSON.stringify(roles));
}

export function getEmail() {
  return sessionStorage.getItem('/jinya/auth/email');
}

export function getPassword() {
  return sessionStorage.getItem('/jinya/auth/password');
}

export function deleteEmail() {
  return sessionStorage.removeItem('/jinya/auth/email');
}

export function deletePassword() {
  return sessionStorage.removeItem('/jinya/auth/password');
}
