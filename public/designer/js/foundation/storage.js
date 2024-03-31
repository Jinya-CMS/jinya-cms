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

export function setDeviceCode(code) {
  localStorage.setItem('/jinya/device/code', code);
}

export function setRedirect(redirect) {
  sessionStorage.setItem('/jinya/login/redirect', redirect);
}

export function getRedirect() {
  return sessionStorage.getItem('/jinya/login/redirect');
}

export function deleteRedirect() {
  sessionStorage.removeItem('/jinya/login/redirect');
}
