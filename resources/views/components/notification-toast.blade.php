<div
    x-data="{ show: false, message: '', type: 'success' }"
    x-on:notify.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type;
        setTimeout(() => show = false, 3000)
    "
    x-show="show"
    x-transition
    class="fixed bottom-5 right-5 z-[100] px-4 py-2 rounded shadow-lg text-white font-semibold"
    :class="{
        'bg-green-600': type === 'success',
        'bg-red-600': type === 'error',
        'bg-blue-600': type === 'info',
        'bg-amber-500': type === 'warning'
    }"
    style="display: none;"
>
    <div class="flex items-center space-x-2">
        <template x-if="type === 'success'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </template>
        <template x-if="type === 'error'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </template>
        <template x-if="type === 'info'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </template>
        <template x-if="type === 'warning'">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </template>

        <span x-text="message"></span>
    </div>
</div>
