export function setRedirect(redirect) {
  sessionStorage.setItem('/jinya/login/redirect', redirect);
}

export function getRedirect() {
  return sessionStorage.getItem('/jinya/login/redirect');
}

export function deleteRedirect() {
  sessionStorage.removeItem('/jinya/login/redirect');
}
