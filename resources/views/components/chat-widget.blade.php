<div x-data="chatWidgetApp()"
    @open-chat-widget.window="isOpen = true; $nextTick(() => scrollBottom())"
    class="fixed bottom-6 right-6 z-[9999] flex flex-col items-end">
    {{-- 1. CỬA SỔ CHAT NHỎ --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-10 scale-95"
        class="w-[380px] bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl flex flex-col mb-4 overflow-hidden"
        style="height: 520px; display: none;">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 py-3 flex items-center justify-between shadow-sm flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></div>
                <div>
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Sales Coach AI</h2>
                    <p class="text-xs text-gray-500">Trợ lý tư vấn bán hàng</p>
                </div>
            </div>
            <button @click="isOpen = false" class="p-1 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Danh sách tin nhắn - flex-1 + overflow-y-auto giữ cố định chiều cao --}}
        <div
            x-ref="messages"
            class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-950 p-4 space-y-4 min-h-0">
            <template x-for="message in allMessages" :key="message.id">
                <div class="flex" :class="message.role === 'user' ? 'justify-end' : 'justify-start'">
                    <div
                        class="max-w-[85%] rounded-xl px-4 py-2.5 text-sm shadow-sm break-words"
                        :class="message.role === 'user'
                            ? 'bg-brand-500 text-white whitespace-pre-wrap'
                            : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-white prose dark:prose-invert prose-sm max-w-none'"
                        x-html="message.role === 'user' ? message.content : renderMarkdown(message.content)"></div>
                </div>
            </template>

            {{-- Typing Loading --}}
            <div x-show="loading" class="flex justify-start" style="display: none;">
                <div class="bg-white dark:bg-gray-800 rounded-xl px-4 py-3 shadow-sm">
                    <div class="flex gap-1.5 items-center h-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce [animation-delay:150ms]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce [animation-delay:300ms]"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ô Nhập Liệu - flex-shrink-0 không bị co lại --}}
        <div class="p-3 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 flex-shrink-0">
            <form @submit.prevent="sendMessage" class="flex gap-2 items-end">
                <textarea
                    x-model="prompt"
                    @keydown.enter.prevent="if(!loading && prompt.trim()) sendMessage()"
                    rows="1"
                    placeholder="Hỏi trợ lý AI..."
                    class="flex-1 resize-none rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                <button
                    type="submit"
                    :disabled="loading || !prompt.trim()"
                    class="p-2 rounded-xl bg-brand-500 text-white hover:opacity-90 disabled:opacity-50 transition-opacity">
                    <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- 2. NÚT TRÒN FLOATING --}}
    <button
        @click="isOpen = !isOpen; if(isOpen) $nextTick(() => scrollBottom())"
        class="w-14 h-14 rounded-full bg-brand-500 text-white flex items-center justify-center shadow-2xl hover:scale-105 transition-transform active:scale-95 focus:outline-none">
        <svg x-show="!isOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <svg x-show="isOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    function chatWidgetApp() {
        return {
            isOpen: false,
            prompt: '',
            loading: false,

            allMessages: [{
                id: 1,
                role: 'assistant',
                content: 'Xin chào, tôi là Sales Coach AI. Tôi có thể giúp gì cho bạn?'
            }],

            renderMarkdown(content) {
                if (typeof marked !== 'undefined') {
                    return marked.parse(content);
                }
                return content;
            },

            async sendMessage() {
                if (!this.prompt.trim()) return;
                const question = this.prompt;
                this.allMessages.push({
                    id: Date.now(),
                    role: 'user',
                    content: question
                });
                this.prompt = '';
                this.loading = true;
                this.scrollBottom();

                try {
                    const response = await fetch('/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            message: question
                        })
                    });
                    const data = await response.json();
                    this.allMessages.push({
                        id: Date.now() + 1,
                        role: 'assistant',
                        content: data.message
                    });
                } catch (e) {
                    this.allMessages.push({
                        id: Date.now() + 2,
                        role: 'assistant',
                        content: 'Đã có lỗi xảy ra. Vui lòng thử lại.'
                    });
                } finally {
                    this.loading = false;
                    this.$nextTick(() => this.scrollBottom());
                }
            },

            scrollBottom() {
                this.$nextTick(() => {
                    if (this.$refs.messages) {
                        this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight;
                    }
                });
            }
        };
    }
</script>