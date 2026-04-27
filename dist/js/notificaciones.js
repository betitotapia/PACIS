(function () {
  'use strict';

  const ENDPOINT = '../../ajax/notificaciones.php';
  const POLL_MS  = 60000; // refresca cada 60 segundos

  const elBadge  = document.getElementById('notif-badge');
  const elHeader = document.getElementById('notif-header');
  const elList   = document.getElementById('notif-list');
  const elBtnAll = document.getElementById('notif-marcar-todas');

  const ICONS = {
    remision : 'bi-file-text',
    factura  : 'bi-receipt',
    recepcion: 'bi-box-arrow-in-down',
    traspaso : 'bi-arrow-left-right',
  };

  function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr.replace(' ', 'T'))) / 1000);
    if (diff < 60)    return 'Ahora mismo';
    if (diff < 3600)  return 'Hace ' + Math.floor(diff / 60) + ' min';
    if (diff < 86400) return 'Hace ' + Math.floor(diff / 3600) + ' h';
    return 'Hace ' + Math.floor(diff / 86400) + ' días';
  }

  function updateCount(total) {
    if (!elBadge) return;
    if (total > 0) {
      elBadge.textContent = total > 9 ? '9+' : total;
      elBadge.style.display = '';
    } else {
      elBadge.style.display = 'none';
    }
  }

  function render(data) {
    if (!elHeader || !elList) return;
    const items = data.items || [];
    const total = data.total || 0;

    updateCount(total);
    elHeader.textContent = total > 0
      ? total + ' notificación' + (total > 1 ? 'es' : '') + ' sin leer'
      : 'Sin notificaciones nuevas';

    elList.innerHTML = items.map(function (n) {
      return [
        '<div class="dropdown-divider"></div>',
        '<a href="#" class="dropdown-item notif-item d-flex align-items-start py-2" data-id="' + n.id_notificacion + '">',
        '  <i class="bi ' + (ICONS[n.tipo] || 'bi-bell') + ' me-2 mt-1 flex-shrink-0"></i>',
        '  <span class="flex-grow-1">' + n.mensaje + '<br>',
        '    <small class="text-secondary">' + timeAgo(n.fecha_creacion) + '</small>',
        '  </span>',
        '</a>',
      ].join('');
    }).join('');

    // Marcar leída al hacer click en un item
    elList.querySelectorAll('.notif-item').forEach(function (el) {
      el.addEventListener('click', function (e) {
        e.preventDefault();
        var id = this.dataset.id;
        fetch(ENDPOINT, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'action=marcar_leida&id=' + id,
        });
        // Quita el divider anterior y el item del DOM
        var prev = this.previousElementSibling;
        if (prev && prev.classList.contains('dropdown-divider')) prev.remove();
        this.remove();
        var remaining = elList.querySelectorAll('.notif-item').length;
        updateCount(remaining);
        elHeader.textContent = remaining > 0
          ? remaining + ' notificación' + (remaining > 1 ? 'es' : '') + ' sin leer'
          : 'Sin notificaciones nuevas';
      });
    });
  }

  function fetchNotifs() {
    fetch(ENDPOINT + '?action=lista')
      .then(function (r) { return r.json(); })
      .then(function (data) { if (data.ok) render(data); })
      .catch(function () { /* red no disponible, ignorar */ });
  }

  // Marcar todas al pulsar el footer del dropdown
  if (elBtnAll) {
    elBtnAll.addEventListener('click', function (e) {
      e.preventDefault();
      fetch(ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=marcar_todas',
      }).then(fetchNotifs);
    });
  }

  // Carga inicial y polling
  fetchNotifs();
  setInterval(fetchNotifs, POLL_MS);
})();
