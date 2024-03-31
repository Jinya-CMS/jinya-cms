import { checkLogin } from './api/authentication.js';
import { Alpine } from '../../../lib/alpine.js';
import { setRedirect } from './storage.js';

export async function needsLogin(context) {
  if (!(await checkLogin())) {
    const redirect = context.path.substring('/designer'.length);
    setRedirect(redirect);

    return context.redirect('/login');
  }

  return null;
}

export async function needsAdmin() {
  if ((await checkLogin()) && Alpine.store('authentication')
    .roles
    .includes('ROLE_ADMIN')) {
    return 'stop';
  }

  return null;
}

export async function needsLogout(context) {
  if (await checkLogin()) {
    return context.redirect('/');
  }

  return null;
}
