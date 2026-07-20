document.addEventListener("DOMContentLoaded", () => {
    // textareaの文字数表示
    const textareas = document.querySelectorAll(
        "textarea[data-counter]"
    );

    textareas.forEach((textarea) => {
        const counterId = textarea.dataset.counter;
        const counter = document.getElementById(counterId);

        const updateCounter = () => {
            counter.textContent =
                `${textarea.value.length}文字`;
        };

        textarea.addEventListener("input", updateCounter);
        updateCounter();
    });

    // 削除前の確認
    const deleteForms = document.querySelectorAll(
        ".delete-form"
    );

    deleteForms.forEach((form) => {
        form.addEventListener("submit", (event) => {
            const result = window.confirm(
                "この記録を削除しますか？"
            );

            if (!result) {
                event.preventDefault();
            }
        });
    });
});