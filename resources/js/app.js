import addressAutocomplete from './addressAutocomplete';

document.addEventListener('alpine:init', () => {
    Alpine.data('addressAutocomplete', addressAutocomplete);
});


window.setupTinyMCE = function(componentId, entangleData) {
    return {
        content: entangleData,
        editorInstance: null,

        initTiny() {
            tinymce.init({
                target: this.$refs.tinyditor,
                inline: true,
                menubar: false,
                branding: false,
                promotion: false,
                fixed_toolbar_container: `#tiny-toolbar-${componentId}`,
                always_show_toolbar: true,
                plugins: 'autolink lists link table wordcount',
                toolbar: 'undo redo | blocks | bold italic underline forecolor backcolor | bullist numlist | link table | removeformat',

                setup: (editor) => {
                    this.editorInstance = editor;

                    // Sync Alpine/Livewire state when the user stops typing or leaves the field
                    editor.on('blur change', () => {
                        this.content = editor.getContent();
                    });

                    // Initial load of content
                    editor.on('init', () => {
                        editor.setContent(this.content || '');
                        // Optional: focus the editor on load
                        // editor.focus();
                    });
                }
            });

            // Sync: Livewire -> TinyMCE
            // This monitors the @entangled property for changes made in PHP
            this.$watch('content', (newValue) => {
                if (this.editorInstance && this.editorInstance.initialized) {
                    const currentTinyContent = this.editorInstance.getContent();
                    const cleanNewValue = newValue ?? '';

                    if (cleanNewValue !== currentTinyContent) {
                        this.editorInstance.setContent(cleanNewValue);
                    }
                }
            });
        },

        /**
         * Clears the editor instance and resets the local content state.
         * Triggered by x-on:section-added.window="clearEditor()" in Blade.
         */
        clearEditor() {
            if (this.editorInstance) {
                this.editorInstance.setContent('');
                this.content = '';

                // Optional: Place cursor back in the editor for the next entry
                this.editorInstance.focus();
            }
        }
    }
}
