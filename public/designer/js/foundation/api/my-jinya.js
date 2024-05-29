import { get, put, upload } from './request.js';

export async function getMyProfile() {
  return get('/api/me');
}

export async function setColorScheme(colorScheme) {
  await put('/api/me/colorscheme', { colorScheme });
}

export async function updateAboutMe(aboutMe) {
  await put('/api/me', { aboutMe });
}

export async function updateProfile(artistName, email) {
  await put('/api/me', {
    artistName,
    email,
  });
}

export async function updateProfilePicture(picture) {
  await upload('/api/me/profilepicture', picture);
}

export function prepareAppTotp() {
  return put('/api/me/otp/app');
}

export async function verifyAppTotp(code) {
  await put('/api/me/otp/app/verify', { code });
}
