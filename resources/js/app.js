window.setupTinyMCE = function(componentId, entangleData) {
    return {
        content: entangleData,
        initTiny() {
            tinymce.init({
                target: this.$refs.tinyditor,
                inline: true,
                menubar: false,
                branding: false,
                promotion: false,
                fixed_toolbar_container: `#tiny-toolbar-${componentId}`,
                always_show_toolbar: true,

                // 1. Ensure 'emoticons' or 'lists' aren't the only plugins
                plugins: 'autolink lists link table wordcount',

                // 2. Add 'forecolor' and 'backcolor' to the toolbar string
                toolbar: 'undo redo | blocks | bold italic underline forecolor backcolor | bullist numlist | link table | removeformat',

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
    }
}
