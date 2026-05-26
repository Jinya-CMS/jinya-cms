import { createFile, uploadFile } from '../../api/files.js';
import { createFolder } from '../../api/folders.js';

function dataUriToFile(dataUri) {
  try {
    const parts = dataUri.split(',');
    if (parts.length < 2) return null;

    const meta = parts[0];
    const mimeMatch = meta.match(/data:([^;]+)/);
    if (!mimeMatch) return null;

    const mime = mimeMatch[1];
    if (mime.indexOf('image/') !== 0) return null;

    const byteString = atob(parts[1]);
    const ab = new ArrayBuffer(byteString.length);
    const ia = new Uint8Array(ab);
    for (let i = 0; i < byteString.length; i++) {
      ia[i] = byteString.charCodeAt(i);
    }
    return new File([ab], '', { type: mime });
  } catch (e) {
    return null;
  }
}

export function getMutationObserver(root, type) {
  return new MutationObserver(async (mutations) => {
    let hasNewImages = false;
    for (let i = 0; i < mutations.length; i++) {
      const added = mutations[i].addedNodes;
      for (let j = 0; j < added.length; j++) {
        const node = added[j];
        if (node.nodeType !== 1) continue; // skip text nodes

        if (node.tagName === 'IMG' && (node.getAttribute('src') || '').indexOf('data:') === 0) {
          hasNewImages = true;
        } else if (node.querySelectorAll) {
          const nested = node.querySelectorAll('img[src^="data:"]');
          if (nested.length > 0) hasNewImages = true;
        }
      }
    }
    if (hasNewImages) {
      const images = root.querySelectorAll('img[src^="data:"]');
      for (const img of images) {
        const file = dataUriToFile(img.getAttribute('src'));
        if (!file) {
          continue;
        }

        const uploadedFile = await createFile(`${type}-${crypto.randomUUID()}`, null, null);
        await uploadFile(uploadedFile.id, file);
        img.setAttribute('src', `/image.php?id=${uploadedFile.id}`);
      }
    }
  });
}
