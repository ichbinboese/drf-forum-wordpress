/**
 * DRF Augenblicke – SharePoint / Fluent-UI Style File & Folder Icons for FileGator
 * Renders crisp SVG icons for Folders, Word (W), Excel (X), PowerPoint (P), PDF, Images, ZIP, Markdown/Text, Media
 */
(function () {
  'use strict';

  var ICONS = {
    folder:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M2 6.5C2 5.12 3.12 4 4.5 4H9.17C9.7 4 10.21 4.21 10.59 4.59L12 6H19.5C20.88 6 22 7.12 22 8.5V17.5C22 18.88 20.88 20 19.5 20H4.5C3.12 20 2 18.88 2 17.5V6.5Z" fill="#FFB900"/>' +
        '<path d="M2 8.5C2 7.67 2.67 7 3.5 7H20.5C21.33 7 22 7.67 22 8.5V17.5C22 18.88 20.88 20 19.5 20H4.5C3.12 20 2 18.88 2 17.5V8.5Z" fill="#FFD147"/>' +
        '<path d="M2 17.5C2 18.88 3.12 20 4.5 20H19.5C20.88 20 22 18.88 22 17.5V17H2V17.5Z" fill="#E3A008" opacity="0.35"/>' +
      '</svg>',

    parent:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M3 7C3 5.9 3.9 5 5 5H9.5L11.5 7H19C20.1 7 21 7.9 21 9V17C21 18.1 20.1 19 19 19H5C3.9 19 3 18.1 3 17V7Z" fill="#E2E8F0" stroke="#94A3B8" stroke-width="1.4"/>' +
        '<path d="M12 15V10.5M12 10.5L9.5 13M12 10.5L14.5 13" stroke="#475569" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>' +
      '</svg>',

    word:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M7 2.5H15.5L20 7V20.5C20 21.33 19.33 22 18.5 22H7C6.17 22 5.5 21.33 5.5 20.5V4C5.5 3.17 6.17 2.5 7 2.5Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.2"/>' +
        '<path d="M15 2.5V7.5H20" fill="#E2E8F0"/>' +
        '<path d="M15 2.5V7.5H20" stroke="#CBD5E1" stroke-width="1.2" stroke-linejoin="round"/>' +
        '<path d="M10.5 11H17.5M10.5 14H17.5M10.5 17H15.5" stroke="#94A3B8" stroke-width="1.3" stroke-linecap="round"/>' +
        '<rect x="2" y="9" width="11" height="10" rx="1.8" fill="#185ABD"/>' +
        '<text x="7.5" y="16.5" fill="#FFFFFF" font-family="Segoe UI, Arial, sans-serif" font-size="7.5" font-weight="700" text-anchor="middle">W</text>' +
      '</svg>',

    excel:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M7 2.5H15.5L20 7V20.5C20 21.33 19.33 22 18.5 22H7C6.17 22 5.5 21.33 5.5 20.5V4C5.5 3.17 6.17 2.5 7 2.5Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.2"/>' +
        '<path d="M15 2.5V7.5H20" fill="#E2E8F0"/>' +
        '<path d="M15 2.5V7.5H20" stroke="#CBD5E1" stroke-width="1.2" stroke-linejoin="round"/>' +
        '<path d="M11 10.5H17.5M11 13.5H17.5M11 16.5H17.5M14.2 10.5V17.5" stroke="#86EFAC" stroke-width="1.2"/>' +
        '<rect x="2" y="9" width="11" height="10" rx="1.8" fill="#107C41"/>' +
        '<text x="7.5" y="16.5" fill="#FFFFFF" font-family="Segoe UI, Arial, sans-serif" font-size="7.8" font-weight="700" text-anchor="middle">X</text>' +
      '</svg>',

    ppt:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M7 2.5H15.5L20 7V20.5C20 21.33 19.33 22 18.5 22H7C6.17 22 5.5 21.33 5.5 20.5V4C5.5 3.17 6.17 2.5 7 2.5Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.2"/>' +
        '<path d="M15 2.5V7.5H20" fill="#E2E8F0"/>' +
        '<path d="M15 2.5V7.5H20" stroke="#CBD5E1" stroke-width="1.2" stroke-linejoin="round"/>' +
        '<circle cx="15" cy="14" r="2.8" fill="#FDBA74"/>' +
        '<rect x="2" y="9" width="11" height="10" rx="1.8" fill="#C43E1C"/>' +
        '<text x="7.5" y="16.5" fill="#FFFFFF" font-family="Segoe UI, Arial, sans-serif" font-size="7.8" font-weight="700" text-anchor="middle">P</text>' +
      '</svg>',

    pdf:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M7 2.5H15.5L20 7V20.5C20 21.33 19.33 22 18.5 22H7C6.17 22 5.5 21.33 5.5 20.5V4C5.5 3.17 6.17 2.5 7 2.5Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.2"/>' +
        '<path d="M15 2.5V7.5H20" fill="#FEE2E2"/>' +
        '<path d="M15 2.5V7.5H20" stroke="#FCA5A5" stroke-width="1.2" stroke-linejoin="round"/>' +
        '<rect x="1.5" y="10" width="15" height="8.5" rx="1.8" fill="#D13438"/>' +
        '<text x="9" y="16.3" fill="#FFFFFF" font-family="Segoe UI, Arial, sans-serif" font-size="6.1" font-weight="800" text-anchor="middle">PDF</text>' +
      '</svg>',

    image:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<rect x="3" y="4" width="18" height="16" rx="2.5" fill="#F5F3FF" stroke="#8B5CF6" stroke-width="1.4"/>' +
        '<circle cx="8.5" cy="9.5" r="1.8" fill="#F59E0B"/>' +
        '<path d="M4.5 17.5L9.5 12.5L13 16L15.8 13.2L19.5 17.5H4.5Z" fill="#7C3AED"/>' +
      '</svg>',

    archive:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M5 3.5H14.5L19 8V19.5C19 20.33 18.33 21 17.5 21H6.5C5.67 21 5 20.33 5 19.5V5C5 4.17 5.67 3.5 6.5 3.5Z" fill="#FEF3C7" stroke="#D97706" stroke-width="1.3"/>' +
        '<rect x="10.5" y="5" width="3" height="2" fill="#B45309"/>' +
        '<rect x="10.5" y="8" width="3" height="2" fill="#B45309"/>' +
        '<rect x="10.5" y="11" width="3" height="2" fill="#B45309"/>' +
        '<rect x="9.8" y="14" width="4.4" height="4" rx="1" fill="#92400E"/>' +
      '</svg>',

    media:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<rect x="3" y="4" width="18" height="16" rx="2.5" fill="#ECFEFF" stroke="#0891B2" stroke-width="1.4"/>' +
        '<polygon points="10,8.5 16.5,12 10,15.5" fill="#0891B2"/>' +
      '</svg>',

    text:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M6 2.5H14.5L19 7V20.5C19 21.33 18.33 22 17.5 22H6C5.17 22 4.5 21.33 4.5 20.5V4C4.5 3.17 5.17 2.5 6 2.5Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.3"/>' +
        '<path d="M14 2.5V7.5H19" fill="#F1F5F9"/>' +
        '<path d="M14 2.5V7.5H19" stroke="#CBD5E1" stroke-width="1.3" stroke-linejoin="round"/>' +
        '<path d="M8 11.5H15.5M8 14.5H15.5M8 17.5H12.5" stroke="#64748B" stroke-width="1.4" stroke-linecap="round"/>' +
      '</svg>',

    generic:
      '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '<path d="M6 2.5H14.5L19 7V20.5C19 21.33 18.33 22 17.5 22H6C5.17 22 4.5 21.33 4.5 20.5V4C4.5 3.17 5.17 2.5 6 2.5Z" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.3"/>' +
        '<path d="M14 2.5V7.5H19" fill="#F1F5F9"/>' +
        '<path d="M14 2.5V7.5H19" stroke="#CBD5E1" stroke-width="1.3" stroke-linejoin="round"/>' +
      '</svg>'
  };

  function getFileType(name, isFolder) {
    if (name === '..') return 'parent';
    if (isFolder) return 'folder';

    var ext = '';
    var idx = name.lastIndexOf('.');
    if (idx !== -1) {
      ext = name.slice(idx + 1).toLowerCase();
    }

    if (['xls', 'xlsx', 'xlsm', 'xlsb', 'ods', 'csv'].indexOf(ext) !== -1) return 'excel';
    if (['doc', 'docx', 'docm', 'odt', 'rtf'].indexOf(ext) !== -1) return 'word';
    if (ext === 'pdf') return 'pdf';
    if (['ppt', 'pptx', 'pptm', 'odp', 'key'].indexOf(ext) !== -1) return 'ppt';
    if (['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'bmp', 'tif', 'tiff', 'raw', 'dng', 'cr2', 'nef', 'arw'].indexOf(ext) !== -1) return 'image';
    if (['zip', 'rar', '7z', 'tar', 'gz', 'bz2'].indexOf(ext) !== -1) return 'archive';
    if (['mp4', 'mov', 'avi', 'mkv', 'webm', 'mp3', 'wav', 'flac', 'ogg'].indexOf(ext) !== -1) return 'media';
    if (['md', 'txt', 'json', 'xml', 'yml', 'yaml', 'html', 'css', 'js', 'php', 'log'].indexOf(ext) !== -1) return 'text';

    return 'generic';
  }

  function decorateFileRows() {
    var tables = document.querySelectorAll('#app table.table');
    tables.forEach(function (table) {
      // Only decorate the file browser table (which has checkbox cells)
      if (!table.querySelector('td.checkbox-cell')) return;

      var rows = table.querySelectorAll('tbody tr');
      rows.forEach(function (row) {
        var nameCell = row.querySelector('td:nth-child(2)');
        if (!nameCell) return;

        // Determine if row is a folder by checking the size column text ("Ordner" / "Folder") or bold tag
        var rowText = row.textContent || '';
        var cells = row.querySelectorAll('td');
        var sizeText = '';
        for (var i = 2; i < cells.length; i++) {
          var t = (cells[i].textContent || '').trim();
          if (t === 'Ordner' || t === 'Folder' || t === 'dir') {
            sizeText = t;
            break;
          }
        }

        // Extract clean file/folder name without our icon
        var rawName = '';
        var nameLink = nameCell.querySelector('a');
        var targetEl = nameLink || nameCell;
        var existingIcon = nameCell.querySelector('.drf-file-icon');

        // Clone text without existing icon
        if (existingIcon) {
          var clone = targetEl.cloneNode(true);
          var ic = clone.querySelector('.drf-file-icon');
          if (ic) ic.remove();
          rawName = (clone.textContent || '').trim();
        } else {
          rawName = (targetEl.textContent || '').trim();
        }

        if (!rawName) return;

        var isFolder = sizeText !== '' || (nameCell.querySelector('strong, b') !== null && rawName.indexOf('.') === -1);
        var type = getFileType(rawName, isFolder);

        // Skip if already decorated with the exact same file & type
        if (existingIcon && existingIcon.getAttribute('data-file-key') === rawName + ':' + type) {
          return;
        }
        if (existingIcon) {
          existingIcon.remove();
        }

        var span = document.createElement('span');
        span.className = 'drf-file-icon drf-icon-' + type;
        span.setAttribute('data-file-key', rawName + ':' + type);
        span.innerHTML = ICONS[type] || ICONS.generic;

        targetEl.classList.add('drf-file-cell');
        targetEl.insertBefore(span, targetEl.firstChild);
      });
    });
  }

  function startObserver() {
    decorateFileRows();
    var app = document.getElementById('app') || document.body;
    var observer = new MutationObserver(function () {
      decorateFileRows();
    });
    observer.observe(app, { childList: true, subtree: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startObserver);
  } else {
    startObserver();
  }
})();
