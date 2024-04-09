import { get, head, httpDelete, post, put } from './request.js';
import { deleteDeviceCode, deleteJinyaApiKey, getJinyaApiKey, setDeviceCode, setJinyaApiKey } from '../storage.js';

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
  const {
    deviceCode,
    apiKey,
  } = await post('/api/login', {
    username: email,
    password,
    twoFactorCode,
  });
  if (deviceCode && apiKey) {
    await setDeviceCode(deviceCode);
    await setJinyaApiKey(apiKey);
  }
}

export async function requestTwoFactor(email, password) {
  await post('/api/2fa', {
    username: email,
    password,
  });
}

export async function deleteApiKey(apiKey) {
  await httpDelete(`/api/api-key/${apiKey}`);
}

export async function logout(fully = false) {
  try {
    await deleteApiKey(getJinyaApiKey());
  } catch {
    console.log('Failed to revoke api key, logging out anyway');
  } finally {
    deleteJinyaApiKey();
    if (fully) {
      deleteDeviceCode();
    }
  }
}

export async function changePassword(oldPassword, newPassword) {
  await put('/api/account/password', {
    oldPassword,
    password: newPassword,
  });
}

export function getApiKeys() {
  return get('/api/api-key');
}

export function locateIp(ip) {
  return get(`/api/ip-location/${ip}`);
}
