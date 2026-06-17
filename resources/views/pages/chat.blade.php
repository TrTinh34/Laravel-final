@extends('layouts.app')

@section('content')
<div
    x-data="chatApp()"
    class="h-[calc(100vh-120px)] flex flex-col overflow-hidden"
>

    {{-- Header --}}
    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-t-xl px-6 py-4"
    >
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                    Sales Coach AI
                </h2>

                <p class="text-sm text-gray-500">
                    AI tư vấn bán hàng
                </p>
            </div>

            <div
                class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700"
            >
                Online
            </div>
        </div>
    </div>

    {{-- Messages --}}
    <div
        id="messages"
        x-ref="messages"
        class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-950 border-x border-gray-200 dark:border-gray-800 p-6 space-y-6"
    >

        <template x-for="message in allMessages" :key="message.id">

            <div
                class="flex"
                :class="message.role === 'user'
                    ? 'justify-end'
                    : 'justify-start'"
            >

                <div
                    class="max-w-4xl rounded-2xl px-5 py-4 shadow-sm"
                    :class="message.role === 'user'
                        ? 'bg-brand-500 text-white whitespace-pre-wrap'
                        : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-white prose dark:prose-invert prose-sm max-w-none'"
                    x-html="message.role === 'user' ? message.content : renderMarkdown(message.content)"
                ></div>

            </div>

        </template>

        {{-- Typing --}}
        <div
            x-show="loading"
            class="flex justify-start"
        >
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl px-5 py-4 shadow-sm"
            >
                <div class="flex gap-2">
                    <span class="w-2 h-2 rounded-full bg-gray-400 animate-bounce"></span>
                    <span class="w-2 h-2 rounded-full bg-gray-400 animate-bounce [animation-delay:150ms]"></span>
                    <span class="w-2 h-2 rounded-full bg-gray-400 animate-bounce [animation-delay:300ms]"></span>
                </div>
            </div>
        </div>

    </div>

    {{-- Input --}}
    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-b-xl p-4"
    >

        <form
            @submit.prevent="sendMessage"
            class="flex gap-3"
        >

            <textarea
                x-model="prompt"
                rows="2"
                placeholder="Nhập câu hỏi..."
                class="flex-1 resize-none rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500"
            ></textarea>

            <button
                type="submit"
                :disabled="loading || !prompt.trim()"
                class="px-6 py-3 rounded-xl bg-brand-500 text-white font-medium hover:bg-brand-600 disabled:opacity-50"
            >
                Gửi
            </button>

        </form>

    </div>
</div>

{{-- Marked.js để render Markdown --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
function chatApp() {
    return {

        prompt: '',

        loading: false,

        allMessages: [
            {
                id: 1,
                role: 'assistant',
                content: 'Xin chào, tôi là Sales Coach AI. Tôi có thể giúp gì cho bạn?'
            }
        ],

        renderMarkdown(content) {
            if (typeof marked !== 'undefined') {
                return marked.parse(content);
            }
            return content;
        },

        async sendMessage() {

            if (!this.prompt.trim()) {
                return;
            }

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
                console.log('Sending message:', question);

                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({
                        message: question
                    })
                });

                const data = await response.json();
                console.log(data);
                this.allMessages.push({
                    id: Date.now() + 1,
                    role: 'assistant',
                    content: data.message
                });

            } catch (e) {

                this.allMessages.push({
                    id: Date.now() + 2,
                    role: 'assistant',
                    content: e.message || 'Đã có lỗi xảy ra. Vui lòng thử lại.'
                });

            } finally {

                this.loading = false;

                this.$nextTick(() => {
                    this.scrollBottom();
                });

            }

        },

        scrollBottom() {

            this.$nextTick(() => {

                this.$refs.messages.scrollTop =
                    this.$refs.messages.scrollHeight;

            });

        }

    };
}
</script>

@endsection