import './bootstrap';

/* ================= ELEMENT ================= */
const chatBox = document.getElementById('chat-box');
const chatForm = document.getElementById('chat-form');
const typingIndicator = document.getElementById('typing-indicator');
const resetBtn = document.getElementById('reset-chat');
const authId = Number(document.querySelector('meta[name="auth-id"]')?.getAttribute('content'));

/* ================= STATE ================= */
let typingTimeout = null;
let currentChatUser = Number(document.getElementById('to_id')?.value || 0);
let unreadCounts = {};

/* ================= RENDER MESSAGE ================= */
function appendMessage(chat) {
    const isMe = Number(chat.from_id) === authId;

    const wrapper = document.createElement('div');
    wrapper.className = `d-flex mb-2 ${isMe ? 'justify-content-end' : 'justify-content-start'}`;

    // avatar lawan
    if (!isMe) {
        const avatar = document.createElement('img');
        avatar.src = `https://i.pravatar.cc/35?img=${chat.from_id}`;
        avatar.className = 'rounded-circle me-2 avatar';
        wrapper.appendChild(avatar);
    }

    // bubble
    const msg = document.createElement('div');
    msg.className = `msg ${isMe ? 'me' : 'them'}`;
    msg.innerHTML = `
        ${chat.message}
        <div class="text-muted small mt-1 text-end">
            ${new Date(chat.created_at).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    })}
        </div>
    `;
    wrapper.appendChild(msg);

    // avatar kita
    if (isMe) {
        const avatar = document.createElement('img');
        avatar.src = `https://i.pravatar.cc/35?img=${authId}`;
        avatar.className = 'rounded-circle ms-2 avatar';
        wrapper.appendChild(avatar);
    }

    chatBox.appendChild(wrapper);
    chatBox.scrollTop = chatBox.scrollHeight;
}

/* ================= TYPING ================= */
function showTyping() {
    typingIndicator.style.display = 'block';
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
        typingIndicator.style.display = 'none';
    }, 1500);
}

function playSound() {
    document.getElementById('notif-sound')?.play();
}

/* ================= SEND MESSAGE ================= */
chatForm?.addEventListener('submit', e => {
    e.preventDefault();
    if (!currentChatUser) return;

    const input = document.getElementById('message');
    const message = input.value.trim();
    if (!message) return;

    axios.post('/chat/send', {
        message,
        to_id: currentChatUser
    }).then(res => {
        appendMessage(res.data);
        input.value = '';
    });
});

/* ================= USER TYPING ================= */
chatForm?.querySelector('#message')?.addEventListener('input', () => {
    if (!currentChatUser) return;
    axios.post('/chat/typing', { to_id: currentChatUser });
});

/* ================= RESET CHAT ================= */
resetBtn?.addEventListener('click', () => {
    if (!currentChatUser) return;
    if (!confirm('Reset seluruh chat dengan user ini?')) return;

    axios.post('/chat/reset', { to_id: currentChatUser });
});

/* ================= MULTI CHAT ADMIN ================= */
document.querySelectorAll('.list-group-item').forEach(el => {
    el.addEventListener('click', () => {
        currentChatUser = Number(el.dataset.userId);

        document.getElementById('user-name').innerText =
            el.querySelector('.user-name')?.innerText || el.innerText.trim();

        resetBtn?.classList.remove('d-none');
        chatBox.innerHTML = '';

        unreadCounts[currentChatUser] = 0;
        document.getElementById(`badge-${currentChatUser}`)?.classList.add('d-none');

        loadChat(currentChatUser);
    });
});

/* ================= REALTIME ================= */
window.Echo.private(`chat.${authId}`)
    .listen('MessageSent', e => {
        const chat = e.chat;

        if (Number(chat.from_id) === authId) return;

        if (Number(chat.from_id) === currentChatUser) {
            appendMessage(chat);
            // playSound();
            axios.post('/chat/read', { from_id: chat.from_id });
        } else {
            unreadCounts[chat.from_id] = (unreadCounts[chat.from_id] || 0) + 1;
            const badge = document.getElementById(`badge-${chat.from_id}`);
            if (badge) {
                badge.innerText = unreadCounts[chat.from_id];
                badge.classList.remove('d-none');
            }
        }
    })
    .listen('ChatReset', e => {
        if (e.userA == authId || e.userB == authId) {
            chatBox.innerHTML = '';
            unreadCounts = {};
            document.querySelectorAll('[id^="badge-"]').forEach(b => b.classList.add('d-none'));
        }
    })
    .listen('UserTyping', e => {
        if (e.fromId == currentChatUser) showTyping();
    });

/* ================= FETCH CHAT ================= */
function loadChat(userId) {
    axios.get(`/chat/fetch?customer_id=${userId}`)
        .then(res => {
            chatBox.innerHTML = '';
            res.data.chats.forEach(chat => appendMessage(chat));

            // 🔥 INI YANG HILANG
            axios.post('/chat/read', {
                from_id: userId
            });
        });
}


/* ================= INIT ================= */
document.addEventListener('DOMContentLoaded', () => {
    if (currentChatUser) loadChat(currentChatUser);
});