@props(['model'])

<div
    class="w-full"
    x-data="{
        content: @entangle($model),
        initTiny() {
            tinymce.init({
                target: $refs.tinyditor,
                inline: true,
                menubar: false,
                branding: false,
                promotion: false,
                fixed_toolbar_container: '#tiny-toolbar-' + $el.id,
                always_show_toolbar: true,
                plugins: 'autolink lists link table wordcount',
                toolbar: 'undo redo | blocks | bold italic | bullist numlist | link table | removeformat',
                setup: (editor) => {
                    editor.on('blur change', () => {
                        this.content = editor.getContent();
                    });

                    editor.on('init', () => {
                        editor.setContent(this.content || '');
                        editor.focus();
                    });
                }
            });
        }
    }"
    x-init="initTiny()"
    id="{{ 'editor-' . $attributes->get('id', Str::random(8)) }}"
>
    <div wire:ignore class="bg-white rounded-lg border border-zinc-200 overflow-hidden shadow-sm">
        {{-- Unique Toolbar for this instance --}}
        <div :id="'tiny-toolbar-' + $el.id" class="bg-zinc-50 border-b border-zinc-200 min-h-[40px]"></div>

        <div
            x-ref="tinyditor"
            class="p-4 min-h-[300px] focus:outline-none text-black max-w-none"
        >
            {!! $attributes->get('value') !!}
        </div>
    </div>

    <style>
        /* This ensures the toolbar is always visible across all instances */
        .tox.tox-tinymce.tox-tinymce-inline {
            display: block !important;
            position: static !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .tox-tinymce-aux { display: none !important; }
    </style>
</div>
