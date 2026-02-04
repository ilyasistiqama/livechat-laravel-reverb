import './bootstrap';

const inboxList = document.getElementById('inbox-list');
const inboxEmpty = document.getElementById('inbox-empty');
const sound = document.getElementById('inbox-sound');
const authId = document.querySelector('meta[name="auth-id"]')?.content;
const authType = document.querySelector('meta[name="auth-type"]')?.content;

/* =========================
   RENDER / UPDATE ITEM
========================= */
function renderInboxItem({
    room_code,
    from_id,
    from_name,
    unread = 0,
    last_message = '',
    chat_href = '',
}) {
    if (!from_id || !inboxList) return;

    let item = inboxList.querySelector(`[data-user-id="${from_id}"]`);

    if (!item) {
        item = document.createElement('a');
        item.dataset.userId = from_id;
        item.href = chat_href; // 🔥 dari backend
        item.className =
            'list-group-item list-group-item-action d-flex justify-content-between align-items-center inbox-item';

        inboxList.prepend(item);
    }

    // CONTENT
    let content = item.querySelector('.inbox-content');
    if (!content) {
        content = document.createElement('div');
        content.className = 'inbox-content overflow-hidden';
        item.appendChild(content);
    }

    // NAME
    let nameEl = content.querySelector('.inbox-name');
    if (!nameEl) {
        nameEl = document.createElement('div');
        nameEl.className = 'fw-semibold inbox-name';
        content.appendChild(nameEl);
    }
    nameEl.innerText = from_name || 'Customer';

    // LAST MESSAGE
    let subEl = content.querySelector('.inbox-sub');
    if (!subEl) {
        subEl = document.createElement('small');
        subEl.className = 'text-muted inbox-sub d-block text-truncate';
        content.appendChild(subEl);
    }
    subEl.innerText = last_message || 'Pesan baru masuk';

    // BADGE
    let badge = item.querySelector('.inbox-badge');
    if (unread > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'badge bg-danger rounded-pill inbox-badge';
            item.appendChild(badge);
        }
        badge.innerText = unread;
    } else {
        badge?.remove();
    }

    inboxEmpty?.classList.add('d-none');
}

/* =========================
   INITIAL LOAD
========================= */
if (authId && inboxList) {
    fetch('/chat/unread')
        .then(res => res.json())
        .then(data => {
            inboxList.innerHTML = '';

            if (!data || data.length === 0) {
                inboxEmpty?.classList.remove('d-none');
                return;
            }

            inboxEmpty?.classList.add('d-none');
            data.forEach(renderInboxItem);
        });
}

/* =========================
   REALTIME UPDATE
========================= */
if (authId && inboxList && window.Echo) {
    Echo.private(`inbox.${authId}`)
        .listen('.InboxUpdated', e => {
            renderInboxItem(e);

            if (sound) {
                sound.currentTime = 0;
                sound.play().catch(() => { });
            }
        });
}
