document.addEventListener("DOMContentLoaded", function () {

    //Register underline as a real Trix attribute
    if (typeof Trix !== 'undefined' && !Trix.config.textAttributes.underline) {
        Trix.config.textAttributes.underline = {
            tagName: "u",
            inheritable: true
        };
    }

    //Add underline button once Trix is initialized
    document.addEventListener("trix-initialize", function (event) {
        const toolbar = event.target.toolbarElement;
        const group = toolbar.querySelector("[data-trix-button-group='text-tools']");

        if (group && !toolbar.querySelector(".trix-button--underline")) {

            const button = document.createElement("button");
            button.type = "button";
            button.classList.add("trix-button", "trix-button--underline");
            button.setAttribute("title", "Underline");
            button.setAttribute("data-trix-attribute", "underline");
            button.innerHTML = "<u>U</u>";
            button.style.fontSize = "18px";
            button.style.position = "relative";

            const greyLine = document.createElement("span");
            greyLine.style.position = "absolute";
            greyLine.style.left = "0";
            greyLine.style.right = "0";
            greyLine.style.height = "1px";
            greyLine.style.backgroundColor = "rgba(0,0,0,0.15)";
            greyLine.style.bottom = "1px";
            greyLine.style.pointerEvents = "none";
            button.appendChild(greyLine);

            group.appendChild(button);
        }
    });

    //Sync Trix content to Livewire 3
    document.addEventListener('trix-change', function(event) {
        const editor = event.target;
        const inputId = editor.getAttribute('input');
        const hiddenInput = document.getElementById(inputId);

        // This triggers the standard DOM 'input' event, which Livewire listens for
        if (hiddenInput) {
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });

});
