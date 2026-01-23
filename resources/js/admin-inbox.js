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

    let item = inboxList.querySelector(`[data-user-id="${from_id}"]`);

    if (!item) {
        item = document.createElement('a');
        item.href = `/chat?customer_id=${from_id}`;
        item.dataset.userId = from_id;
        item.className =
            'flex justify-between items-center p-2 rounded hover:bg-gray-100 text-gray-800';

        inboxList.prepend(item);
    }

    // ===== NAME =====
    let nameEl = item.querySelector('.inbox-name');
    if (!nameEl) {
        nameEl = document.createElement('span');
        nameEl.className = 'inbox-name';
        item.prepend(nameEl);
    }
    nameEl.innerText = from_name;

    // ===== BADGE =====
    let badge = item.querySelector('.inbox-badge');

    if (unread > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.className =
                'ml-2 min-w-[20px] text-center bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full inbox-badge';
            item.appendChild(badge);
        }
        badge.innerText = unread;
    } else {
        badge?.remove();
    }

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
            data.forEach(renderInboxItem);
        });
}

/* =========================
   REALTIME UPDATE
========================= */
if (authId && inboxList) {
    Echo.private(`inbox.${authId}`)
        .listen('.InboxUpdated', (e) => {

            renderInboxItem(e);
            sound?.play().catch(() => { });
        });
}
