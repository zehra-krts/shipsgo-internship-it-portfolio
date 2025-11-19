import { showToast } from "../../utils/toast";
const MAX_LENGTH = 100;
export function inlineEdit(
    labelEl: HTMLLabelElement,
    taskId: number,
    onCommit: (id: number, newText: string) => void,
    onCancel: () => void
): void {
    const previousText = labelEl.textContent ?? "";
    const inputEl = document.createElement("input");
    inputEl.type = "text";
    inputEl.value = previousText;
    inputEl.classList.add("task__edit-input");

    labelEl.replaceWith(inputEl);
    inputEl.focus();

    const commit = () => {
        const updated = inputEl.value.trim();

        if (!updated) {
            showToast("Task cannot be empty!");
            inputEl.replaceWith(labelEl);
            onCancel();
            return;
        }

        if (updated.length > MAX_LENGTH) {
            showToast(`Task text is too long! Max ${MAX_LENGTH} characters.`);
            inputEl.replaceWith(labelEl);
            onCancel();
            return;
        }

        if (updated !== previousText) onCommit(taskId, updated);
        else {
            inputEl.replaceWith(labelEl);
            onCancel();
        }
    };

    inputEl.addEventListener("keydown", (e: KeyboardEvent) => {
        if (e.key === "Enter") commit();
        if (e.key === "Escape") {
            inputEl.replaceWith(labelEl);
            onCancel();
        }
    });
    inputEl.addEventListener("blur", commit);
}
