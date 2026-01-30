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
let activeChannel = null;

/* ================= STATE ================= */
let typingTimeout = null;
let unreadCounts = {};

/* ================= RENDER MESSAGE ================= */
function appendMessage(chat) {
    const isMe = Number(chat.from_id) === authId && chat.from_type === authType;

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
            ${new Date(chat.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
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
    typingIndicator.classList.remove('d-none');

    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
        typingIndicator.classList.add('d-none');
    }, 1500);
}

/* ================= SEND MESSAGE ================= */
chatForm?.addEventListener('submit', e => {
    e.preventDefault();
    const message = document.getElementById('message').value.trim();

    console.log(currentChatUser);
    if (!message || !currentChatUser) return;

    axios.post('/chat/send', {
        message,
        to_id: currentChatUser,
        to_type: document.getElementById('to_type').value,
        room_code: currentRoom,
        type: currentChatType
    }).then(res => {
        appendMessage(res.data);

        // 🔥 JANGAN PAKAI if (!currentRoom)
        currentRoom = res.data.room_code;
        document.getElementById('room_code').value = currentRoom;

        subscribeRoomChannel(currentRoom);

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
    if (!confirm('Reset seluruh chat di room ini?')) return;

    axios.post('/chat/reset', { room_code: currentRoom }).then(() => {
        chatBox.innerHTML = '';
        unreadCounts = {};
    });
});

/* ================= MULTI CHAT ADMIN ================= */
document.querySelectorAll('.list-group-item').forEach(el => {
    el.addEventListener('click', () => {
        currentChatUser = Number(el.dataset.userId);
        currentRoom = el.dataset.roomCode || null; // pastikan ada room_code dari backend
        currentChatType = el.dataset.chatType || 'customer-to-admin';

        document.getElementById('user-name').innerText =
            el.querySelector('.user-name')?.innerText || el.innerText.trim();

        resetBtn?.classList.remove('d-none');
        chatBox.innerHTML = '';
        unreadCounts[currentChatUser] = 0;
        document.getElementById(`badge-${currentChatUser}`)?.classList.add('d-none');

        if (currentRoom) {
            loadChat(currentRoom);
            subscribeRoomChannel(currentRoom);
        }
    });
});

/* ================= REALTIME ================= */
function subscribeRoomChannel(roomCode) {
    if (!roomCode) return;

    const channelName = `chat.${roomCode}`;

    // 🚫 kalau sudah subscribe ke room ini, stop
    if (activeChannel === channelName) return;

    // 🚪 leave channel lama
    if (activeChannel) {
        window.Echo.leave(activeChannel);
    }

    activeChannel = channelName;

    window.Echo.private(channelName)
        .listen('MessageSent', e => {
            const chat = e.chat;

            if (
                Number(chat.from_id) === authId &&
                chat.from_type === authType
            ) return;

            appendMessage(chat);
            axios.post('/chat/read', { room_code: roomCode });
        })

        .listen('ChatReset', e => {
            if (e.room_code === currentRoom) {

                if (activeChannel) {
                    window.Echo.leave(activeChannel);
                    activeChannel = null;
                }

                chatBox.innerHTML = '';
                currentRoom = null;
                document.getElementById('room_code').value = '';
            }
        })

        .listen('UserTyping', e => {
            if (
                Number(e.from_id) === currentChatUser &&
                e.from_type !== authType
            ) {
                showTyping();
            }
        });
}

/* ================= FETCH CHAT ================= */
function loadChat(roomCode) {
    if (!roomCode) return;

    axios.get(`/chat/fetch`, { params: { room_code: roomCode } })
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
