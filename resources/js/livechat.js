import './bootstrap';

/* ================= ELEMENT ================= */
const chatBox = document.getElementById('chat-box');
const chatForm = document.getElementById('chat-form');
const typingIndicator = document.getElementById('typing-indicator');
const resetBtn = document.getElementById('reset-chat');

const authType = document.querySelector('meta[name="auth-type"]').content;
const authId = Number(document.querySelector('meta[name="auth-id"]').content);

let currentRoom = document.getElementById('room_code')?.value || null;
let currentChatUser = Number(document.getElementById('to_id')?.value || 0);
let currentChatType = document.getElementById('chat_type')?.value || 'customer-to-admin';

let activeRoomChannel = null;
let typingTimeout = null;

/* ================= RENDER MESSAGE ================= */
function appendMessage(chat) {
    const isMe = Number(chat.from_id) === authId && chat.from_type === authType;

    const wrapper = document.createElement('div');
    wrapper.className = `d-flex mb-2 ${isMe ? 'justify-content-end' : 'justify-content-start'}`;

    if (!isMe) {
        const avatar = document.createElement('img');
        avatar.src = `https://i.pravatar.cc/35?img=${chat.from_id}`;
        avatar.className = 'rounded-circle me-2 avatar';
        wrapper.appendChild(avatar);
    }

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
    typingIndicator.classList.remove('d-none');
    clearTimeout(typingTimeout);

    typingTimeout = setTimeout(() => {
        typingIndicator.classList.add('d-none');
    }, 1500);
}

/* ================= USER CHANNEL (GLOBAL) ================= */
window.Echo.private(`user.${authId}`)
    .listen('MessageSentToUser', e => {
        const chat = e.chat;

        // 🔥 KALAU ROOM SUDAH AKTIF → ABAIKAN
        if (currentRoom) return;

        // chat pertama → assign room
        currentRoom = chat.room_code;
        document.getElementById('room_code').value = currentRoom;
        subscribeRoomChannel(currentRoom);

        appendMessage(chat);
    });


/* ================= SEND MESSAGE ================= */
chatForm?.addEventListener('submit', e => {
    e.preventDefault();

    const message = document.getElementById('message').value.trim();
    if (!message || !currentChatUser) return;

    axios.post('/chat/send', {
        message,
        to_id: currentChatUser,
        to_type: document.getElementById('to_type').value,
        room_code: currentRoom,
        type: currentChatType
    }).then(res => {
        appendMessage(res.data);

        if (!currentRoom) {
            currentRoom = res.data.room_code;
            document.getElementById('room_code').value = currentRoom;
            subscribeRoomChannel(currentRoom);
        }

        document.getElementById('message').value = '';
    });
});

/* ================= USER TYPING ================= */
chatForm?.querySelector('#message')?.addEventListener('input', () => {
    if (!currentRoom) return;
    axios.post('/chat/typing', { room_code: currentRoom });
});

/* ================= RESET CHAT ================= */
resetBtn?.addEventListener('click', () => {
    if (!currentRoom) return;
    if (!confirm('Akhiri chat di room ini?')) return;

    axios.post('/chat/reset', { room_code: currentRoom }).then(() => {
        cleanupRoom();
        window.location.href = window.routes.dashboard;
    });
});

/* ================= ROOM CHANNEL ================= */
function subscribeRoomChannel(roomCode) {
    if (!roomCode) return;

    const channelName = `chat.${roomCode}`;

    if (activeRoomChannel === channelName) return;

    if (activeRoomChannel) {
        window.Echo.leave(activeRoomChannel);
    }

    activeRoomChannel = channelName;

    window.Echo.private(channelName)
        .listen('MessageSent', e => {
            const chat = e.chat;

            if (chat.room_code !== currentRoom) return;

            if (
                Number(chat.from_id) === authId &&
                chat.from_type === authType
            ) return;

            appendMessage(chat);
            axios.post('/chat/read', { room_code: roomCode });
        })

        .listen('UserTyping', e => {
            if (currentChatType === 'customer-to-admin' &&
                Number(e.from_id) === currentChatUser &&
                e.from_type !== authType
            ) {
                showTyping();
            } else if (currentChatType === 'customer-to-customer' && Number(e.from_id) === currentChatUser &&
                e.from_type === authType) {
                showTyping();
            }
        })

        .listen('ChatReset', e => {
            if (e.room_code === currentRoom) {
                alert('Chat telah diakhiri.');
                cleanupRoom();
                window.location.href = window.routes.dashboard;
            }
        });
}

/* ================= CLEANUP ================= */
function cleanupRoom() {
    chatBox.innerHTML = '';
    document.getElementById('room_code').value = '';
    currentRoom = null;

    if (activeRoomChannel) {
        window.Echo.leave(activeRoomChannel);
        activeRoomChannel = null;
    }
}

/* ================= FETCH CHAT ================= */
function loadChat(roomCode) {
    if (!roomCode) return;

    axios.get('/chat/fetch', { params: { room_code: roomCode } })
        .then(res => {
            chatBox.innerHTML = '';
            res.data.chats.forEach(chat => appendMessage(chat));
            axios.post('/chat/read', { room_code: roomCode });
        });
}

/* ================= INIT ================= */
document.addEventListener('DOMContentLoaded', () => {
    if (currentRoom) {
        loadChat(currentRoom);
        subscribeRoomChannel(currentRoom);
    }
});
