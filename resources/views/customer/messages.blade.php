@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-4 space-y-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('شات واتساب — مسار customer') }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('نفس خطوات التوثيق: جلب شاتات، رسائل، بروكسي ميديا، إرسال، استطلاع (poll)، تحميل أقدم (load-more)، إرسال ميديا. يمكن تمرير session_id في الرابط.') }}
            </p>
            @if ($resolvedSessionId)
                <p class="text-xs text-emerald-700 dark:text-emerald-300">
                    {{ __('الجلسة الحالية:') }} <code class="text-[11px]">{{ $resolvedSessionId }}</code>
                </p>
            @endif
        </div>

        <div id="customer-chat-routes" class="hidden"
            data-chats-url="{{ route('whatsapp.services.api.chats') }}"
            data-messages-url="{{ route('whatsapp.services.api.messages') }}"
            data-media-url="{{ route('customer.messages.media') }}"
            data-send-url="{{ route('customer.messages.send') }}"
            data-poll-url="{{ route('customer.messages.poll') }}"
            data-load-more-url="{{ route('customer.messages.load-more') }}"
            data-send-media-url="{{ route('customer.messages.send-media') }}"
            data-session-id="{{ $resolvedSessionId ?? '' }}"
            data-poll-interval="{{ (int) $pollIntervalMs }}"
            data-initial-chat="{{ $initialChatId ?? '' }}">
        </div>

        <div
            class="bg-white dark:bg-[#161615] border border-gray-200 dark:border-[#3E3E3A] rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="p-4 sm:p-5 border-b border-gray-200 dark:border-[#3E3E3A] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('محادثات الواتساب') }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('استطلاع تلقائي للرسائل عند اختيار شات (يمكن ضبط الفترة من WHATSAPP_POLL_INTERVAL_MS).') }}
                    </p>
                    <p id="customer-live-status" class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1 hidden"
                        aria-live="polite"></p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                        <input type="checkbox" id="customer-poll-enabled" checked class="rounded border-gray-300">
                        {{ __('تفعيل الاستطلاع') }}
                    </label>
                    <button type="button" id="customer-chats-refresh"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                        {{ __('تحديث القائمة') }}
                    </button>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row min-h-[min(70vh,560px)] max-h-[min(85vh,720px)]">
                <aside
                    class="w-full lg:w-[min(100%,320px)] lg:flex-shrink-0 border-b lg:border-b-0 lg:border-e border-gray-200 dark:border-[#3E3E3A] flex flex-col max-h-[40vh] lg:max-h-none">
                    <div id="customer-chats-list" class="flex-1 overflow-y-auto p-2 space-y-1 text-sm">
                        <p class="text-xs text-gray-500 dark:text-gray-400 p-3">
                            {{ __('جاري التحميل…') }}
                        </p>
                    </div>
                </aside>
                <section class="flex-1 flex flex-col min-h-0 bg-gray-50/50 dark:bg-[#0f0f0e]">
                    <div
                        class="px-4 py-3 border-b border-gray-200 dark:border-[#3E3E3A] flex flex-wrap items-center justify-between gap-2">
                        <span id="customer-chat-header-title"
                            class="text-sm font-medium text-gray-800 dark:text-gray-100 flex-1 min-w-0 truncate">
                            {{ __('اختر محادثة من القائمة') }}
                        </span>
                        <div class="flex flex-wrap gap-1">
                            <button type="button" id="customer-messages-refresh" disabled
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 disabled:opacity-40 disabled:cursor-not-allowed">
                                {{ __('تحديث الرسائل') }}
                            </button>
                            <button type="button" id="customer-load-more" disabled
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-[#222220] border border-gray-200 dark:border-[#3E3E3A] disabled:opacity-40 disabled:cursor-not-allowed">
                                {{ __('رسائل أقدم') }}
                            </button>
                        </div>
                    </div>
                    <div id="customer-messages-wrap" class="flex-1 overflow-y-auto p-3 space-y-2 min-h-[200px]">
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center py-8">
                            {{ __('لم تُحدد محادثة بعد.') }}
                        </p>
                    </div>
                    <form id="customer-reply-form" class="border-t border-gray-200 dark:border-[#3E3E3A] p-3 space-y-2 bg-white dark:bg-[#161615]">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $resolvedSessionId ?? '' }}">
                        <input type="hidden" name="chat_id" id="customer-reply-chat-id" value="">
                        <label for="customer-reply-message" class="sr-only">{{ __('رسالة') }}</label>
                        <textarea name="message" id="customer-reply-message" rows="2" disabled
                            class="block w-full rounded-xl border border-gray-300 dark:border-[#3E3E3A] dark:bg-[#161615] dark:text-gray-100 text-sm px-3 py-2 focus:ring-2 focus:ring-emerald-500 disabled:opacity-50"
                            placeholder="{{ __('اكتب ردك هنا…') }}"></textarea>
                        <div class="flex justify-end">
                            <button type="submit" id="customer-reply-submit" disabled
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ __('إرسال') }}
                            </button>
                        </div>
                    </form>
                    <form id="customer-send-media-form" enctype="multipart/form-data"
                        class="border-t border-gray-200 dark:border-[#3E3E3A] p-3 space-y-2 bg-white dark:bg-[#161615]">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $resolvedSessionId ?? '' }}">
                        <input type="hidden" name="phone_number" id="customer-send-media-phone" value="">
                        <p class="text-[11px] text-gray-500">{{ __('إرسال ملف (صورة/صوت/فيديو) — يُستخدم معرف المحادثة كـ phone_number.') }}
                        </p>
                        <div class="flex flex-wrap gap-2 items-end">
                            <input type="file" name="media" id="customer-media-file" accept="image/*,audio/*,video/*,.pdf"
                                disabled
                                class="text-xs text-gray-700 dark:text-gray-200 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:bg-emerald-600 file:text-white disabled:opacity-50" />
                            <button type="submit" id="customer-send-media-submit" disabled
                                class="px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-gray-700 hover:bg-gray-800 disabled:opacity-50">
                                {{ __('رفع ميديا') }}
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const routesEl = document.getElementById('customer-chat-routes');
            if (!routesEl) return;

            const chatsUrl = routesEl.dataset.chatsUrl;
            const messagesUrl = routesEl.dataset.messagesUrl;
            const mediaProxyUrl = routesEl.dataset.mediaUrl || '';
            const sendUrl = routesEl.dataset.sendUrl || '';
            const pollUrl = routesEl.dataset.pollUrl || '';
            const loadMoreUrl = routesEl.dataset.loadMoreUrl || '';
            const sendMediaUrl = routesEl.dataset.sendMediaUrl || '';
            const sessionIdFixed = (routesEl.dataset.sessionId || '').trim();
            const pollIntervalMs = parseInt(routesEl.dataset.pollInterval || '1000', 10) || 1000;
            const initialChat = (routesEl.dataset.initialChat || '').trim();

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const pollCheckbox = document.getElementById('customer-poll-enabled');

            let selectedChatId = null;
            let selectedChatLabel = '';
            let pollTimer = null;
            let loadMoreOffset = 0;

            const chatListEl = document.getElementById('customer-chats-list');
            const liveStatusEl = document.getElementById('customer-live-status');
            const chatHeaderTitleEl = document.getElementById('customer-chat-header-title');
            const messagesWrap = document.getElementById('customer-messages-wrap');
            const replyChatId = document.getElementById('customer-reply-chat-id');
            const replyMessage = document.getElementById('customer-reply-message');
            const replySubmit = document.getElementById('customer-reply-submit');
            const replyForm = document.getElementById('customer-reply-form');
            const btnRefreshChats = document.getElementById('customer-chats-refresh');
            const btnMessagesRefresh = document.getElementById('customer-messages-refresh');
            const btnLoadMore = document.getElementById('customer-load-more');
            const sendMediaForm = document.getElementById('customer-send-media-form');
            const sendMediaPhone = document.getElementById('customer-send-media-phone');
            const mediaFile = document.getElementById('customer-media-file');
            const sendMediaSubmit = document.getElementById('customer-send-media-submit');

            function withSession(url) {
                const u = new URL(url, window.location.origin);
                if (sessionIdFixed) u.searchParams.set('session_id', sessionIdFixed);
                return u.toString();
            }

            function extractChats(payload) {
                if (!payload || !payload.ok) return [];
                const d = payload.data;
                if (Array.isArray(d)) return d;
                if (d && Array.isArray(d.chats)) return d.chats;
                if (d && d.data && Array.isArray(d.data.chats)) return d.data.chats;
                if (d && d.data && d.data.data && Array.isArray(d.data.data.chats)) return d.data.data.chats;
                if (d && Array.isArray(d.data)) return d.data;
                if (d && Array.isArray(d.items)) return d.items;
                return [];
            }

            function chatIdOf(c) {
                return c.id || c.chatId || c._serialized || '';
            }

            function chatLabelOf(c) {
                return c.name || c.pushName || c.formattedTitle || chatIdOf(c) || '—';
            }

            function extractMessages(payload) {
                if (!payload || !payload.ok) return [];
                const d = payload.data;
                if (Array.isArray(d)) return d;
                if (d && Array.isArray(d.messages)) return d.messages;
                if (d && d.data && Array.isArray(d.data.messages)) return d.data.messages;
                if (d && d.data && d.data.data && Array.isArray(d.data.data.messages)) return d.data.data.messages;
                if (d && Array.isArray(d.data)) return d.data;
                return [];
            }

            function messageText(m) {
                if (typeof m === 'string') return m;
                const b = m.body;
                if (typeof b === 'string') return b;
                if (b && typeof b === 'object' && b.text) return b.text;
                return m.caption || m.message || m.text || (m.type ? ('[' + m.type + ']') : '');
            }

            function safeMediaUrl(u) {
                if (!u || typeof u !== 'string') return null;
                const t = u.trim();
                if (t.startsWith('https://') || t.startsWith('http://') || t.startsWith('blob:') || t.startsWith(
                        'data:audio/')) return t;
                return null;
            }

            function getAudioMessageUrl(m) {
                if (!m || typeof m !== 'object') return null;
                const tryList = [
                    m.mediaUrl, m.media_url, m.url, m.downloadUrl, m.download_url,
                    m.audioUrl, m.audio_url, m.voiceUrl,
                    m.audio && m.audio.url,
                    m.voice && m.voice.url,
                    m.message && m.message.audioMessage && m.message.audioMessage.url,
                    m.message && m.message.pttMessage && m.message.pttMessage.url,
                    m.body && typeof m.body === 'object' && m.body.audioMessage && m.body.audioMessage.url,
                    m._data && m._data.url,
                    m.raw && m.raw.message && m.raw.message.audioMessage && m.raw.message.audioMessage.url
                ];
                for (let i = 0; i < tryList.length; i++) {
                    const x = tryList[i];
                    const s = safeMediaUrl(x);
                    if (s) return s;
                }
                if (typeof m.audioBase64 === 'string' && m.audioMime) {
                    return 'data:' + m.audioMime + ';base64,' + m.audioBase64;
                }
                return null;
            }

            function getWhatsappMessageId(m) {
                if (!m || typeof m !== 'object') return null;
                if (m.key && typeof m.key === 'object' && typeof m.key.id === 'string' && m.key.id.length) {
                    return m.key.id;
                }
                if (typeof m.key === 'string' && m.key.length) {
                    try {
                        const parsed = JSON.parse(m.key);
                        if (parsed && typeof parsed.id === 'string' && parsed.id.length) {
                            return parsed.id;
                        }
                    } catch (e) {}
                }
                if (m.msg && m.msg.key && typeof m.msg.key.id === 'string') return m.msg.key.id;
                if (m.message && m.message.key && typeof m.message.key.id === 'string') {
                    return m.message.key.id;
                }
                if (typeof m.messageId === 'string' && m.messageId.length) return m.messageId;
                if (typeof m.msgId === 'string' && m.msgId.length) return m.msgId;
                if (typeof m.id === 'string' && m.id.length && m.id.indexOf('_') !== -1) return m.id;
                return null;
            }

            function hydrateVoiceAudio(audioEl, playbackUrl, directUrl) {
                audioEl.preload = 'auto';
                audioEl.crossOrigin = 'anonymous';
                const tryBlobFromUrl = async (u) => {
                    try {
                        const r = await fetch(u, {
                            credentials: 'same-origin'
                        });
                        if (!r.ok) return false;
                        const buf = await r.arrayBuffer();
                        if (buf.byteLength < 16) return false;
                        const ct = (r.headers.get('content-type') || '').split(';')[0].trim();
                        const mime = ct && ct.indexOf('audio') !== -1 ? ct : 'audio/ogg';
                        const blob = new Blob([buf], {
                            type: mime
                        });
                        const objUrl = URL.createObjectURL(blob);
                        audioEl.src = objUrl;
                        audioEl.addEventListener('ended', () => URL.revokeObjectURL(objUrl), {
                            once: true
                        });
                        return true;
                    } catch (e) {
                        return false;
                    }
                };
                audioEl.addEventListener('error', async function onVoiceErr() {
                    audioEl.removeEventListener('error', onVoiceErr);
                    if (directUrl && directUrl !== playbackUrl) {
                        audioEl.src = directUrl;
                        audioEl.addEventListener('error', async function onVoiceErr2() {
                            audioEl.removeEventListener('error', onVoiceErr2);
                            await tryBlobFromUrl(playbackUrl);
                        }, {
                            once: true
                        });
                        return;
                    }
                    await tryBlobFromUrl(playbackUrl);
                }, {
                    once: true
                });
            }

            function getVoicePlaybackUrl(m, chatJid) {
                const mid = getWhatsappMessageId(m);
                if (mid && mediaProxyUrl) {
                    const u = new URL(withSession(mediaProxyUrl), window.location.origin);
                    u.searchParams.set('message_id', mid);
                    if (chatJid) u.searchParams.set('chat_id', chatJid);
                    return u.toString();
                }
                return getAudioMessageUrl(m);
            }

            function isVoiceOrAudioMessage(m) {
                if (!m || typeof m !== 'object') return false;
                const t = String(m.type || m.messageType || m.msgType || '').toLowerCase();
                if (['ptt', 'audio', 'voice', 'audio_message', 'ptt_message'].indexOf(t) !== -1) return true;
                if (m.ptt === true) return true;
                if (m.message && (m.message.audioMessage || m.message.pttMessage)) return true;
                if (m.body && typeof m.body === 'object' && m.body.audioMessage) return true;
                if (getAudioMessageUrl(m)) return true;
                const mime = (m.mimetype || m.mimeType || (m.message && m.message.audioMessage && m.message
                    .audioMessage.mimetype) || '').toLowerCase();
                if (mime.indexOf('audio') !== -1) return true;
                return false;
            }

            function voiceDurationLabel(m) {
                const s = m.seconds || m.duration || (m.message && m.message.audioMessage && m.message.audioMessage
                    .seconds);
                if (typeof s === 'number' && s > 0) return Math.round(s) + 's';
                return '';
            }

            function isFromMe(m) {
                return m.fromMe === true || m.fromMe === 1 || m.from_me === true;
            }

            function formatTs(m) {
                const t = m.timestamp || m.t;
                if (!t) return '';
                const ms = t < 1e12 ? t * 1000 : t;
                try {
                    return new Date(ms).toLocaleString();
                } catch (e) {
                    return '';
                }
            }

            function touchLiveStatus() {
                if (!liveStatusEl) return;
                liveStatusEl.classList.remove('hidden');
                liveStatusEl.textContent = '{{ __('آخر تحديث:') }} ' + new Date().toLocaleTimeString();
            }

            function renderMessageRows(msgs) {
                messagesWrap.innerHTML = '';
                if (!msgs.length) {
                    messagesWrap.innerHTML =
                        '<p class="text-xs text-gray-500 text-center py-6">{{ __('لا توجد رسائل في هذه الدفعة.') }}</p>';
                    return;
                }
                msgs.forEach((m) => {
                    const row = document.createElement('div');
                    const mine = isFromMe(m);
                    row.className = 'flex ' + (mine ? 'justify-end' : 'justify-start');
                    const bubble = document.createElement('div');
                    bubble.className = 'max-w-[85%] rounded-2xl px-3 py-2 text-xs sm:text-sm ' +
                        (mine ?
                            'bg-emerald-600 text-white' :
                            'bg-white dark:bg-[#222220] text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-[#3E3E3A]');
                    const chatJidForMedia = selectedChatId || (m.key && m.key.remoteJid) || '';
                    const directAudioUrl = getAudioMessageUrl(m);
                    const playbackUrl = getVoicePlaybackUrl(m, chatJidForMedia);
                    const voice = isVoiceOrAudioMessage(m);

                    if (voice && playbackUrl) {
                        const voiceWrap = document.createElement('div');
                        voiceWrap.className = 'space-y-2';
                        const voiceHead = document.createElement('div');
                        voiceHead.className = 'text-[11px] opacity-90 flex flex-wrap items-center gap-2';
                        voiceHead.appendChild(document.createTextNode('🎤 '));
                        const voiceTitle = document.createElement('span');
                        voiceTitle.textContent = @json(__('رسالة صوتية'));
                        voiceHead.appendChild(voiceTitle);
                        const durStr = voiceDurationLabel(m);
                        if (durStr) {
                            const durEl = document.createElement('span');
                            durEl.className = 'opacity-75 tabular-nums';
                            durEl.textContent = durStr;
                            voiceHead.appendChild(durEl);
                        }
                        const audioEl = document.createElement('audio');
                        audioEl.controls = true;
                        audioEl.className = 'block w-full min-w-[200px] max-w-[min(100%,280px)] h-9';
                        audioEl.setAttribute('src', playbackUrl);
                        hydrateVoiceAudio(audioEl, playbackUrl, directAudioUrl);
                        voiceWrap.appendChild(voiceHead);
                        voiceWrap.appendChild(audioEl);
                        bubble.appendChild(voiceWrap);
                    } else if (voice && !playbackUrl) {
                        const hint = document.createElement('div');
                        hint.className = 'whitespace-pre-wrap break-words text-[11px]';
                        hint.textContent = '🎤 ' + @json(__('رسالة صوتية — لا يوجد رابط تشغيل في بيانات الـ API.'));
                        bubble.appendChild(hint);
                    } else {
                        const text = document.createElement('div');
                        text.className = 'whitespace-pre-wrap break-words';
                        text.textContent = messageText(m) || '—';
                        bubble.appendChild(text);
                    }
                    const meta = document.createElement('div');
                    meta.className = 'text-[10px] mt-1 opacity-80';
                    meta.textContent = formatTs(m);
                    if (formatTs(m)) bubble.appendChild(meta);
                    row.appendChild(bubble);
                    messagesWrap.appendChild(row);
                });
            }

            function renderChatListItems(items, applyDeepLink) {
                chatListEl.innerHTML = '';
                items.forEach((c) => {
                    const id = chatIdOf(c);
                    if (!id) return;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.setAttribute('data-chat-id', id);
                    const unread = (typeof c.unreadCount === 'number' && c.unreadCount > 0) ?
                        ' · ' + c.unreadCount :
                        '';
                    btn.className =
                        'w-full text-start px-3 py-2 rounded-lg text-xs sm:text-sm transition ' +
                        (id === selectedChatId ?
                            'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-900 dark:text-emerald-100' :
                            'hover:bg-gray-100 dark:hover:bg-[#222220] text-gray-800 dark:text-gray-100');
                    btn.textContent = chatLabelOf(c) + unread;
                    btn.addEventListener('click', () => selectChat(id, chatLabelOf(c)));
                    chatListEl.appendChild(btn);
                });
                if (applyDeepLink && initialChat) {
                    const found = items.find((x) => chatIdOf(x) === initialChat);
                    if (found) selectChat(initialChat, chatLabelOf(found));
                    else selectChat(initialChat, initialChat);
                }
            }

            async function loadChats(refresh) {
                chatListEl.innerHTML =
                    '<p class="text-xs text-gray-500 p-3">{{ __('جاري التحميل…') }}</p>';
                try {
                    const u = new URL(withSession(chatsUrl), window.location.origin);
                    if (refresh) u.searchParams.set('refresh', '1');
                    const res = await fetch(u.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    const items = extractChats(json);
                    if (!json.ok) {
                        chatListEl.innerHTML = '<p class="text-xs text-red-600 dark:text-red-400 p-3">' + (json
                            .message || '{{ __('تعذر التحميل') }}') + '</p>';
                        return;
                    }
                    if (items.length === 0) {
                        chatListEl.innerHTML =
                            '<p class="text-xs text-gray-500 p-3">{{ __('لا توجد محادثات أو القائمة فارغة.') }}</p>';
                        if (initialChat) selectChat(initialChat, initialChat);
                        return;
                    }
                    renderChatListItems(items, true);
                    touchLiveStatus();
                } catch (e) {
                    chatListEl.innerHTML =
                        '<p class="text-xs text-red-600 p-3">{{ __('فشل الاتصال بالخادم.') }}</p>';
                }
            }

            async function loadMessages(chatId, scrollBottom) {
                if (!chatId) return;
                loadMoreOffset = 0;
                try {
                    const u = new URL(withSession(messagesUrl), window.location.origin);
                    u.searchParams.set('chat_id', chatId);
                    u.searchParams.set('limit', '80');
                    u.searchParams.set('order', 'asc');
                    const res = await fetch(u.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    const msgs = extractMessages(json);
                    if (!json.ok) {
                        messagesWrap.innerHTML = '<p class="text-xs text-red-600 p-3">' + (json.message ||
                            '{{ __('تعذر جلب الرسائل') }}') + '</p>';
                        return;
                    }
                    renderMessageRows(msgs);
                    loadMoreOffset = msgs.length;
                    if (scrollBottom !== false) {
                        messagesWrap.scrollTop = messagesWrap.scrollHeight;
                    }
                    touchLiveStatus();
                } catch (e) {
                    messagesWrap.innerHTML =
                        '<p class="text-xs text-red-600 p-3">{{ __('فشل تحميل الرسائل.') }}</p>';
                }
            }

            async function pollMessages() {
                if (!selectedChatId || !pollUrl || !pollCheckbox?.checked) return;
                try {
                    const u = new URL(withSession(pollUrl), window.location.origin);
                    u.searchParams.set('chat_id', selectedChatId);
                    u.searchParams.set('limit', '40');
                    const res = await fetch(u.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    if (json.success && json.data && Array.isArray(json.data.messages)) {
                        renderMessageRows(json.data.messages);
                        messagesWrap.scrollTop = messagesWrap.scrollHeight;
                        touchLiveStatus();
                    }
                } catch (e) {}
            }

            function startPoll() {
                stopPoll();
                if (!pollUrl || !pollCheckbox?.checked) return;
                pollTimer = setInterval(pollMessages, pollIntervalMs);
            }

            function stopPoll() {
                if (pollTimer) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
            }

            async function loadOlder() {
                if (!selectedChatId || !loadMoreUrl) return;
                try {
                    const u = new URL(withSession(loadMoreUrl), window.location.origin);
                    u.searchParams.set('chat_id', selectedChatId);
                    u.searchParams.set('offset', String(loadMoreOffset));
                    u.searchParams.set('limit', '30');
                    const res = await fetch(u.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    if (!json.success || !json.data || !Array.isArray(json.data.messages)) return;
                    const prevScroll = messagesWrap.scrollHeight;
                    const older = json.data.messages;
                    const frag = document.createDocumentFragment();
                    older.forEach((m) => {
                        const row = document.createElement('div');
                        const mine = isFromMe(m);
                        row.className = 'flex ' + (mine ? 'justify-end' : 'justify-start');
                        const bubble = document.createElement('div');
                        bubble.className = 'max-w-[85%] rounded-2xl px-3 py-2 text-xs sm:text-sm ' +
                            (mine ?
                                'bg-emerald-600 text-white' :
                                'bg-white dark:bg-[#222220] text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-[#3E3E3A]');
                        const text = document.createElement('div');
                        text.className = 'whitespace-pre-wrap break-words';
                        text.textContent = messageText(m) || '—';
                        bubble.appendChild(text);
                        row.appendChild(bubble);
                        frag.appendChild(row);
                    });
                    messagesWrap.insertBefore(frag, messagesWrap.firstChild);
                    messagesWrap.scrollTop = messagesWrap.scrollHeight - prevScroll;
                    loadMoreOffset = json.data.nextOffset != null ? json.data.nextOffset : (loadMoreOffset +
                        older.length);
                    if (!json.data.hasMore) btnLoadMore.disabled = true;
                } catch (e) {}
            }

            function selectChat(id, label) {
                selectedChatId = id;
                selectedChatLabel = label || id;
                if (chatHeaderTitleEl) chatHeaderTitleEl.textContent = selectedChatLabel;
                replyChatId.value = id;
                sendMediaPhone.value = id;
                replyMessage.disabled = false;
                replySubmit.disabled = false;
                mediaFile.disabled = false;
                sendMediaSubmit.disabled = false;
                btnMessagesRefresh.disabled = false;
                btnLoadMore.disabled = false;
                chatListEl.querySelectorAll('button[data-chat-id]').forEach((btn) => {
                    const bid = btn.getAttribute('data-chat-id');
                    const isSel = bid === id;
                    btn.className =
                        'w-full text-start px-3 py-2 rounded-lg text-xs sm:text-sm transition ' +
                        (isSel ?
                            'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-900 dark:text-emerald-100' :
                            'hover:bg-gray-100 dark:hover:bg-[#222220] text-gray-800 dark:text-gray-100');
                });
                void loadMessages(id, true);
                startPoll();
            }

            btnRefreshChats.addEventListener('click', () => loadChats(true));
            btnMessagesRefresh.addEventListener('click', () => {
                if (selectedChatId) void loadMessages(selectedChatId, true);
            });
            btnLoadMore.addEventListener('click', () => loadOlder());
            pollCheckbox?.addEventListener('change', () => {
                if (pollCheckbox.checked) startPoll();
                else stopPoll();
            });

            if (replyForm && sendUrl) {
                replyForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (!selectedChatId || !replyMessage.value.trim()) return;
                    replySubmit.disabled = true;
                    const fd = new FormData(replyForm);
                    try {
                        const res = await fetch(sendUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: fd,
                            credentials: 'same-origin',
                        });
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            let msg = data.message || '';
                            if (data.errors && typeof data.errors === 'object') {
                                const k = Object.keys(data.errors)[0];
                                if (k && data.errors[k] && data.errors[k][0]) msg = data.errors[k][0];
                            }
                            if (!msg) msg = @json(__('فشل إرسال الرسالة.'));
                            alert(msg);
                            replySubmit.disabled = false;
                            return;
                        }
                        if (data.success) {
                            replyMessage.value = '';
                            touchLiveStatus();
                            await loadMessages(selectedChatId, true);
                        }
                    } catch (err) {
                        alert(@json(__('فشل الاتصال بالخادم.')));
                    }
                    replySubmit.disabled = false;
                });
            }

            if (sendMediaForm && sendMediaUrl) {
                sendMediaForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (!selectedChatId || !mediaFile.files || !mediaFile.files.length) return;
                    sendMediaSubmit.disabled = true;
                    const fd = new FormData(sendMediaForm);
                    try {
                        const res = await fetch(sendMediaUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: fd,
                            credentials: 'same-origin',
                        });
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || !data.success) {
                            alert(data.message || @json(__('فشل إرسال الملف.')));
                            sendMediaSubmit.disabled = false;
                            return;
                        }
                        mediaFile.value = '';
                        await loadMessages(selectedChatId, true);
                    } catch (err) {
                        alert(@json(__('فشل الاتصال بالخادم.')));
                    }
                    sendMediaSubmit.disabled = false;
                });
            }

            void loadChats(false);
        });
    </script>
@endpush
