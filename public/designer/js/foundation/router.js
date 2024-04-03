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

export async function fetchScript({ path }) {
  if (path.startsWith('/login') || path.startsWith('/designer/login')) {
    // eslint-disable-next-line
    await import('/designer/js/login/login.js');
  } else {
    const [, , stage, area, page] = path.split('/');
    if (stage && area) {
      await import(`/designer/js/${stage}/${area}/${page ?? 'index'}.js`);
      Alpine.store('navigation')
        .navigate({
          stage,
          area,
          page: page ?? 'index',
        });
    }
  }
}
