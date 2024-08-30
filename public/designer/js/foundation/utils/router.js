import { checkLogin } from '../api/authentication.js';
import { Alpine } from '../../../../lib/alpine.js';
import { setRedirect } from './storage.js';
import { getAuthenticationDatabase } from '../database/authentication.js';

export function needsLogin(context) {
  if (getAuthenticationDatabase().isApiKeyValid()) {
    return null;
  }

  const redirect = context.path.substring('/designer'.length);
  setRedirect(redirect);

  return context.redirect('/login');
}

export async function needsAdmin() {
  if (!((await checkLogin()) && Alpine.store('authentication').roles.includes('ROLE_ADMIN'))) {
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

export async function fetchScript({ route }) {
  if (route.startsWith('/login') || route.startsWith('/designer/login')) {
    await import('/designer/js/login/login.js');
  } else {
    const [, , stage, area, page] = route.split('/');
    if (stage && area) {
      await import(`/designer/js/${stage}/${area}/${page ?? 'index'}.js`);
      Alpine.store('navigation').navigate({
        stage,
        area,
        page: page ?? 'index',
      });
    }
  }
}
