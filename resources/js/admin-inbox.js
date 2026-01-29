import './bootstrap';

const inboxList = document.getElementById('inbox-list');
const sound = document.getElementById('inbox-sound');
const authId = document
    .querySelector('meta[name="auth-id"]')
    ?.getAttribute('content');

/* =========================
   RENDER / UPDATE ITEM
========================= */
function renderInboxItem({ from_id, from_name, unread }) {
    if (!inboxList) return;

    let item = inboxList.querySelector(`[data-user-id="${from_id}"]`);

    if (!item) {
        item = document.createElement('a');
        item.href = `/chat?customer_id=${from_id}`;
        item.dataset.userId = from_id;

        item.className =
            'list-group-item list-group-item-action d-flex justify-content-between align-items-center inbox-item';

        inboxList.prepend(item);
    }

    /* ===== LEFT CONTENT ===== */
    let content = item.querySelector('.inbox-content');
    if (!content) {
        content = document.createElement('div');
        content.className = 'inbox-content';
        item.appendChild(content);
    }

    /* ===== NAME ===== */
    let nameEl = content.querySelector('.inbox-name');
    if (!nameEl) {
        nameEl = document.createElement('div');
        nameEl.className = 'fw-semibold inbox-name';
        content.appendChild(nameEl);
    }
    nameEl.innerText = from_name;

    /* ===== SUBTEXT ===== */
    let subEl = content.querySelector('.inbox-sub');
    if (!subEl) {
        subEl = document.createElement('small');
        subEl.className = 'text-muted inbox-sub';
        subEl.innerText = 'Pesan baru masuk';
        content.appendChild(subEl);
    }

    /* ===== BADGE ===== */
    let badge = item.querySelector('.inbox-badge');

    if (unread > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.className =
                'badge bg-danger rounded-pill inbox-badge';
            item.appendChild(badge);
        }
        badge.innerText = unread;
    } else {
        badge?.remove();
    }

    /* PIN TO TOP */
    inboxList.prepend(item);
}

/* =========================
   INITIAL LOAD
========================= */
if (authId && inboxList) {
    fetch('/chat/unread')
        .then(res => res.json())
        .then(data => {
            inboxList.innerHTML = '';

            if (data.length === 0) {
                document.getElementById('inbox-empty')?.classList.remove('d-none');
                return;
            }

            document.getElementById('inbox-empty')?.classList.add('d-none');
            data.forEach(renderInboxItem);
        });
}

/* =========================
   REALTIME UPDATE
========================= */
if (authId && inboxList && window.Echo) {
    Echo.private(`inbox.${authId}`)
        .listen('.InboxUpdated', (e) => {

            renderInboxItem(e);

            if (sound) {
                sound.currentTime = 0;
                sound.play().catch(() => { });
            }
        });
}
