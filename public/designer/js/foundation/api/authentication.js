import { get, head, httpDelete, post, put } from './request.js';
import { getAuthenticationDatabase } from '../database/authentication.js';

const authenticationDatabase = getAuthenticationDatabase();

export async function checkLogin() {
  try {
    await head('/api/login');

    return true;
  } catch {
    return false;
  }
}

export async function checkKnownDevice(device) {
  try {
    await head(`/api/known-device/${device}`);

    return true;
  } catch {
    return false;
  }
}

export async function login(email, password, twoFactorCode = null) {
  const { deviceCode, apiKey } = await post('/api/login', {
    username: email,
    password,
    twoFactorCode,
  });
  if (deviceCode && apiKey) {
    await authenticationDatabase.setApiKey(apiKey);
  }
}

export async function requestTwoFactor(email, password) {
  await post('/api/2fa', {
    username: email,
    password,
  });
}

export function getApiKeys() {
  return get('/api/api-key');
}

export async function deleteApiKey(apiKey) {
  await httpDelete(`/api/api-key/${apiKey}`);
}

export function getKnownDevices() {
  return get('/api/known-device');
}

export function deleteKnownDevice(device) {
  return httpDelete(`/api/known-device/${device}`);
}

export function locateIp(ip) {
  return get(`/api/ip-location/${ip}`);
}

export async function logout(fully = false) {
  if (fully) {
    try {
      await deleteKnownDevice(await authenticationDatabase.getDeviceCode());
    } catch {
      console.log('Failed to forget device, logging out anyway');
    }
  }

  await httpDelete('/api/logout');
  await authenticationDatabase.deleteApiKey();
}

export async function changePassword(oldPassword, newPassword) {
  await put('/api/account/password', {
    oldPassword,
    password: newPassword,
  });
}
