import localize, { getLanguage } from '../utils/localize.js';
import filePicker from './filePicker.js';

import '../../../lib/jodit/jodit.js';

function setJoditIcons() {
  Jodit.modules.Icon.icons = {
    crop: '<svg viewBox="0 0 24 24" class="lucide lucide-crop"><path d="M6 2v14a2 2 0 0 0 2 2h14"/><path d="M18 22V8a2 2 0 0 0-2-2H2"/></svg>',
    resize:
      '<svg viewBox="0 0 24 24" class="lucide lucide-scaling"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M14 15H9v-5"/><path d="M16 3h5v5"/><path d="M21 3 9 15"/></svg>',
    enter:
      '<svg viewBox="0 0 24 24" class="lucide lucide-corner-down-left"><polyline points="9 10 4 15 9 20"/><path d="M20 4v7a4 4 0 0 1-4 4H4"/></svg>',
    bold: '<svg viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bold"><path d="M6 12h9a4 4 0 0 1 0 8H7a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h7a4 4 0 0 1 0 8"/></svg>',
    italic:
      '<svg viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-italic"><line x1="19" x2="10" y1="4" y2="4"/><line x1="14" x2="5" y1="20" y2="20"/><line x1="15" x2="9" y1="4" y2="20"/></svg>',
    strikethrough:
      '<svg viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-strikethrough"><path d="M16 4H9a3 3 0 0 0-2.83 4"/><path d="M14 12a4 4 0 0 1 0 8H6"/><line x1="4" x2="20" y1="12" y2="12"/></svg>',
    subscript:
      '<svg viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-subscript"><path d="m4 5 8 8"/><path d="m12 5-8 8"/><path d="M20 19h-4c0-1.5.44-2 1.5-2.5S20 15.33 20 14c0-.47-.17-.93-.48-1.29a2.11 2.11 0 0 0-2.62-.44c-.42.24-.74.62-.9 1.07"/></svg>',
    superscript:
      '<svg viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-superscript"><path d="m4 19 8-8"/><path d="m12 19-8-8"/><path d="M20 12h-4c0-1.5.442-2 1.5-2.5S20 8.334 20 7.002c0-.472-.17-.93-.484-1.29a2.105 2.105 0 0 0-2.617-.436c-.42.239-.738.614-.899 1.06"/></svg>',
    underline:
      '<svg viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-underline"><path d="M6 4v6a6 6 0 0 0 12 0V4"/><line x1="4" x2="20" y1="20" y2="20"/></svg>',
    eraser:
      '<svg viewBox="0 0 24 24" class="lucide lucide-eraser"><path d="m7 21-4.3-4.3c-1-1-1-2.5 0-3.4l9.6-9.6c1-1 2.5-1 3.4 0l5.6 5.6c1 1 1 2.5 0 3.4L13 21"/><path d="M22 21H7"/><path d="m5 11 9 9"/></svg>',
    copy: '<svg viewBox="0 0 24 24" class="lucide lucide-copy"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>',
    cut: '<svg viewBox="0 0 24 24" class="lucide lucide-scissors"><circle cx="6" cy="6" r="3"/><path d="M8.12 8.12 12 12"/><path d="M20 4 8.12 15.88"/><circle cx="6" cy="18" r="3"/><path d="M14.8 14.8 20 20"/></svg>',
    paste:
      '<svg viewBox="0 0 24 24" class="lucide lucide-clipboard"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/></svg>',
    'select-all':
      '<svg viewBox="0 0 24 24" class="lucide lucide-box-select"><path d="M5 3a2 2 0 0 0-2 2"/><path d="M19 3a2 2 0 0 1 2 2"/><path d="M21 19a2 2 0 0 1-2 2"/><path d="M5 21a2 2 0 0 1-2-2"/><path d="M9 3h1"/><path d="M9 21h1"/><path d="M14 3h1"/><path d="M14 21h1"/><path d="M3 9v1"/><path d="M21 9v1"/><path d="M3 14v1"/><path d="M21 14v1"/></svg>',
    palette:
      '<svg viewBox="0 0 24 24" class="lucide lucide-palette"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/></svg>',
    brush:
      '<svg viewBox="0 0 24 24" class="lucide lucide-brush"><path d="m9.06 11.9 8.07-8.06a2.85 2.85 0 1 1 4.03 4.03l-8.06 8.08"/><path d="M7.07 14.94c-1.66 0-3 1.35-3 3.02 0 1.33-2.5 1.52-2 2.02 1.08 1.1 2.49 2.02 4 2.02 2.2 0 4-1.8 4-4.04a3.01 3.01 0 0 0-3-3.02z"/></svg>',
    copyformat:
      '<svg viewBox="0 0 24 24" class="lucide lucide-paint-roller"><rect width="16" height="6" x="2" y="2" rx="2"/><path d="M10 16v-2a2 2 0 0 1 2-2h8a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect width="4" height="6" x="8" y="16" rx="1"/></svg>',
    font: '<svg viewBox="0 0 24 24" class="lucide lucide-type"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" x2="15" y1="20" y2="20"/><line x1="12" x2="12" y1="4" y2="20"/></svg>',
    fontsize:
      '<svg viewBox="0 0 24 24" class="lucide lucide-a-large-small"><path d="M21 14h-5"/><path d="M16 16v-3.5a2.5 2.5 0 0 1 5 0V16"/><path d="M4.5 13h6"/><path d="m3 16 4.5-9 4.5 9"/></svg>',
    paragraph:
      '<svg viewBox="0 0 24 24" class="lucide lucide-pilcrow"><path d="M13 4v16"/><path d="M17 4v16"/><path d="M19 4H9.5a4.5 4.5 0 0 0 0 9H13"/></svg>',
    fullsize:
      '<svg viewBox="0 0 24 24" class="lucide lucide-expand"><path d="m21 21-6-6m6 6v-4.8m0 4.8h-4.8"/><path d="M3 16.2V21m0 0h4.8M3 21l6-6"/><path d="M21 7.8V3m0 0h-4.8M21 3l-6 6"/><path d="M3 7.8V3m0 0h4.8M3 3l6 6"/></svg>',
    shrink:
      '<svg viewBox="0 0 24 24" class="lucide lucide-shrink"><path d="m15 15 6 6m-6-6v4.8m0-4.8h4.8"/><path d="M9 19.8V15m0 0H4.2M9 15l-6 6"/><path d="M15 4.2V9m0 0h4.8M15 9l6-6"/><path d="M9 4.2V9m0 0H4.2M9 9 3 3"/></svg>',
    hr: '<svg viewBox="0 0 24 24" class="lucide lucide-minus"><path d="M5 12h14"/></svg>',
    image:
      '<svg viewBox="0 0 24 24" class="lucide lucide-image"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>',
    indent:
      '<svg viewBox="0 0 24 24" class="lucide lucide-indent-increase"><polyline points="3 8 7 12 3 16"/><line x1="21" x2="11" y1="12" y2="12"/><line x1="21" x2="11" y1="6" y2="6"/><line x1="21" x2="11" y1="18" y2="18"/></svg>',
    outdent:
      '<svg viewBox="0 0 24 24" class="lucide lucide-indent-decrease"><polyline points="7 8 3 12 7 16"/><line x1="21" x2="11" y1="12" y2="12"/><line x1="21" x2="11" y1="6" y2="6"/><line x1="21" x2="11" y1="18" y2="18"/></svg>',
    addcolumn:
      '<svg viewBox="0 0 24 24" class="lucide lucide-between-vertical-start"><rect width="7" height="13" x="3" y="8" rx="1"/><path d="m15 2-3 3-3-3"/><rect width="7" height="13" x="14" y="8" rx="1"/></svg>',
    addrow:
      '<svg viewBox="0 0 24 24" class="lucide lucide-between-horizontal-start"><rect width="13" height="7" x="8" y="3" rx="1"/><path d="m2 9 3 3-3 3"/><rect width="13" height="7" x="8" y="14" rx="1"/></svg>',
    merge:
      '<svg viewBox="0 0 24 24" class="lucide lucide-table-cells-merge"><path d="M12 21v-6"/><path d="M12 9V3"/><path d="M3 15h18"/><path d="M3 9h18"/><rect width="18" height="18" x="3" y="3" rx="2"/></svg>',
    th: '<svg viewBox="0 0 1792 1792"> <path d="M512 1248v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm0-512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm640 512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm-640-1024v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm640 512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm640 512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm-640-1024v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm640 512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm0-512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68z"/> </svg>',
    splitg:
      '<svg viewBox="0 0 24 24" class="lucide lucide-table-rows-split"><path d="M14 10h2"/><path d="M15 22v-8"/><path d="M15 2v4"/><path d="M2 10h2"/><path d="M20 10h2"/><path d="M3 19h18"/><path d="M3 22v-6a2 2 135 0 1 2-2h14a2 2 45 0 1 2 2v6"/><path d="M3 2v2a2 2 45 0 0 2 2h14a2 2 135 0 0 2-2V2"/><path d="M8 10h2"/><path d="M9 22v-8"/><path d="M9 2v4"/></svg>',
    splitv:
      '<svg viewBox="0 0 24 24" class="lucide lucide-table-columns-split"><path d="M14 14v2"/><path d="M14 20v2"/><path d="M14 2v2"/><path d="M14 8v2"/><path d="M2 15h8"/><path d="M2 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H2"/><path d="M2 9h8"/><path d="M22 15h-4"/><path d="M22 3h-2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h2"/><path d="M22 9h-4"/><path d="M5 3v18"/></svg>',
    'th-list':
      '<svg viewBox="0 0 1792 1792"> <path d="M512 1248v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm0-512v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm1280 512v192q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h960q40 0 68 28t28 68zm-1280-1024v192q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h320q40 0 68 28t28 68zm1280 512v192q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h960q40 0 68 28t28 68zm0-512v192q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h960q40 0 68 28t28 68z"/> </svg>',
    justify:
      '<svg viewBox="0 0 24 24" class="lucide lucide-align-justify"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>',
    'line-height':
      '<svg viewBox="0 0 24 24"> <path d="M5.09668 6.99707H7.17358L4.17358 3.99707L1.17358 6.99707H3.09668V17.0031H1.15881L4.15881 20.0031L7.15881 17.0031H5.09668V6.99707Z"/> <path d="M22.8412 7H8.84119V5H22.8412V7Z"/> <path d="M22.8412 11H8.84119V9H22.8412V11Z"/> <path d="M8.84119 15H22.8412V13H8.84119V15Z"/> <path d="M22.8412 19H8.84119V17H22.8412V19Z"/> </svg>',
    link: '<svg viewBox="0 0 24 24" class="lucide lucide-link"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
    unlink:
      '<svg viewBox="0 0 24 24" class="lucide lucide-unlink"><path d="m18.84 12.25 1.72-1.71h-.02a5.004 5.004 0 0 0-.12-7.07 5.006 5.006 0 0 0-6.95 0l-1.72 1.71"/><path d="m5.17 11.75-1.71 1.71a5.004 5.004 0 0 0 .12 7.07 5.006 5.006 0 0 0 6.95 0l1.71-1.71"/><line x1="8" x2="8" y1="2" y2="5"/><line x1="2" x2="5" y1="8" y2="8"/><line x1="16" x2="16" y1="19" y2="22"/><line x1="19" x2="22" y1="16" y2="16"/></svg>',
    ol: '<svg viewBox="0 0 24 24" class="lucide lucide-list-ordered"><line x1="10" x2="21" y1="6" y2="6"/><line x1="10" x2="21" y1="12" y2="12"/><line x1="10" x2="21" y1="18" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>',
    ul: '<svg viewBox="0 0 24 24" class="lucide lucide-list"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>',
    print:
      '<svg viewBox="0 0 24 24" class="lucide lucide-printer"><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>',
    redo: '<svg viewBox="0 0 24 24" class="lucide lucide-redo"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"/></svg>',
    undo: '<svg viewBox="0 0 24 24" class="lucide lucide-undo"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/></svg>',
    search:
      '<svg viewBox="0 0 24 24" class="lucide lucide-search"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
    source:
      '<svg viewBox="0 0 24 24" class="lucide lucide-code"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
    spellcheck:
      '<svg viewBox="0 0 24 24" class="lucide lucide-spell-check"><path d="m6 16 6-12 6 12"/><path d="M8 12h8"/><path d="m16 20 2 2 4-4"/></svg>',
    symbols:
      '<svg viewBox="0 0 24 24" class="lucide lucide-pi"><line x1="9" x2="9" y1="4" y2="20"/><path d="M4 7c0-1.7 1.3-3 3-3h13"/><path d="M18 20c-1.7 0-3-1.3-3-3V4"/></svg>',
    table:
      '<svg viewBox="0 0 24 24" class="lucide lucide-table"><path d="M12 3v18"/><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/></svg>',
    video:
      '<svg viewBox="0 0 24 24" class="lucide lucide-video"><path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"/><rect x="2" y="6" width="14" height="12" rx="2"/></svg>',
    'angle-down': '<svg viewBox="0 0 24 24" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>',
    'angle-left': '<svg viewBox="0 0 24 24" class="lucide lucide-chevron-left"><path d="m15 18-6-6 6-6"/></svg>',
    'angle-right': '<svg viewBox="0 0 24 24" class="lucide lucide-chevron-right"><path d="m9 18 6-6-6-6"/></svg>',
    'angle-up': '<svg viewBox="0 0 24 24" class="lucide lucide-chevron-up"><path d="m18 15-6-6-6 6"/></svg>',
    bin: '<svg viewBox="0 0 24 24" class="lucide lucide-trash"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>',
    cancel:
      '<svg viewBox="0 0 24 24" class="lucide lucide-ban"><circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/></svg>',
    center:
      '<svg viewBox="0 0 24 24" class="lucide lucide-align-center"><line x1="21" x2="3" y1="6" y2="6"/><line x1="17" x2="7" y1="12" y2="12"/><line x1="19" x2="5" y1="18" y2="18"/></svg>',
    check: '<svg viewBox="0 0 24 24" class="lucide lucide-check"><path d="M20 6 9 17l-5-5"/></svg>',
    chevron: '<svg viewBox="0 0 24 24" class="lucide lucide-chevron-down"><path d="m6 9 6 6 6-6"/></svg>',
    dots: '<svg viewBox="0 0 24 24" class="lucide lucide-ellipsis-vertical"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>',
    eye: '<svg viewBox="0 0 24 24" class="lucide lucide-eye"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>',
    file: '<svg viewBox="0 0 24 24" class="lucide lucide-file"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>',
    folder:
      '<svg viewBox="0 0 24 24" class="lucide lucide-folder"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>',
    'info-circle':
      '<svg viewBox="0 0 24 24" class="lucide lucide-info"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
    left: '<svg viewBox="0 0 24 24" class="lucide lucide-align-left"><line x1="21" x2="3" y1="6" y2="6"/><line x1="15" x2="3" y1="12" y2="12"/><line x1="17" x2="3" y1="18" y2="18"/></svg>',
    lock: '<svg viewBox="0 0 24 24" class="lucide lucide-lock"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
    ok: '<svg viewBox="0 0 24 24" class="lucide lucide-check"><path d="M20 6 9 17l-5-5"/></svg>',
    pencil:
      '<svg viewBox="0 0 24 24" class="lucide lucide-pencil"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>',
    plus: '<svg viewBox="0 0 24 24" class="lucide lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>',
    'resize-handler':
      '<svg viewBox="0 0 24 24" class="lucide lucide-grip"><circle cx="12" cy="5" r="1"/><circle cx="19" cy="5" r="1"/><circle cx="5" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/><circle cx="12" cy="19" r="1"/><circle cx="19" cy="19" r="1"/><circle cx="5" cy="19" r="1"/></svg>',
    right:
      '<svg viewBox="0 0 24 24" class="lucide lucide-align-right"><line x1="21" x2="3" y1="6" y2="6"/><line x1="21" x2="9" y1="12" y2="12"/><line x1="21" x2="7" y1="18" y2="18"/></svg>',
    save: '<svg viewBox="0 0 24 24" class="lucide lucide-save"><path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/><path d="M7 3v4a1 1 0 0 0 1 1h7"/></svg>',
    settings:
      '<svg viewBox="0 0 24 24" class="lucide lucide-bolt"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><circle cx="12" cy="12" r="4"/></svg>',
    unlock:
      '<svg viewBox="0 0 24 24" class="lucide lucide-lock-open"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>',
    update:
      '<svg viewBox="0 0 24 24" class="lucide lucide-refresh-cw"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>',
    upload:
      '<svg viewBox="0 0 24 24" class="lucide lucide-upload"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>',
    valign:
      '<svg viewBox="0 0 24 24" class="lucide lucide-move-vertical"><polyline points="8 18 12 22 16 18"/><polyline points="8 6 12 2 16 6"/><line x1="12" x2="12" y1="2" y2="22"/></svg>',
  };
}

async function insertImage(editor) {
  const fileResult = await filePicker({
    title: localize({ key: 'jodit.insert_image' }),
  });
  editor.s.insertImage(`/image.php?id=${fileResult.id}`, null, 300);
  editor.synchronizeValues();
}

const imageToolbarButton = {
  icon: 'image',
  tooltip: localize({ key: 'jodit.insert_image' }),
  exec: (editor) => {
    insertImage(editor);
  },
};

function setContextMenu(editor) {
  const { ContextMenu } = Jodit.modules;
  const contextMenu = new ContextMenu(editor);
  editor.events.on(editor.editor, 'contextmenu', (e) => {
    e.preventDefault();

    if (editor.selection.isCollapsed()) {
      contextMenu.show(e.clientX, e.clientY, [
        {
          title: localize({ key: 'jodit.insert_image' }),
          icon: 'image',
          exec: () => {
            insertImage(editor);
          },
        },
      ]);
    }
  });
}

function getInlineToolbar() {
  return [
    'bold',
    'italic',
    'underline',
    'strikethrough',
    '|',
    'brush',
    'fontsize',
    '|',
    'ul',
    'ol',
    'paragraph',
    'link',
    'align',
    '|',
    imageToolbarButton,
  ];
}

function getFullToolbar() {
  return [
    'bold',
    'italic',
    'underline',
    'strikethrough',
    '|',
    'brush',
    'fontsize',
    '|',
    'ul',
    'ol',
    'paragraph',
    'link',
    'align',
    '|',
    imageToolbarButton,
    '|',
    'indent',
    'outdent',
  ];
}

export function createJodit(idOrElement, inline = false, height = undefined) {
  setJoditIcons();
  const data = {
    toolbar: !inline,
    showCharsCounter: false,
    showWordsCounter: false,
    showXPathInStatusbar: false,
    minHeight: '11rem',
    disablePlugins:
      'about,add-new-line,ai-assistant,class-span,clean-html,clipboard,copyformat,dtd,file,font,hr,iframe,image,image-properties,indent,key-arrow-outside,line-height,mobile,xpath,table-keyboard-navigation,tab,symbols,stat,spellcheck,speech-recognize,search,resize-cells,redo-undo,print,preview,powered-by-jodit,paste-storage,paste-from-word,video,wrap-nodes,limit',
    inline,
    toolbarInline: true,
    toolbarInlineForSelection: true,
    showPlaceholder: false,
    language: getLanguage(),
    popup: {
      selection: Jodit.atom(getInlineToolbar()),
      toolbar: Jodit.atom(getInlineToolbar()),
    },
    sourceEditorCDNUrlsJS: '',
    beautifyHTMLCDNUrlsJS: '',
  };
  if (height) {
    data.height = height;
  }
  if (!inline) {
    data.buttons = getFullToolbar();
    data.extraButtons = [
      'fullsize',
      'source',
    ];
  }
  const editor = Jodit.make(idOrElement, data);
  setContextMenu(editor);

  return editor;
}
