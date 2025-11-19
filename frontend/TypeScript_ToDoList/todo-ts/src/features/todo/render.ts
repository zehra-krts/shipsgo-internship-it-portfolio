import type { ITask } from "../../types/task";
import { createElement, Trash2, Pencil, Check } from "lucide";

type THandlers = {
    onToggle(id: number): void;
    onDelete(id: number): void;

    onStartInlineEdit(labelEl: HTMLLabelElement, taskId: number, commit: (id: number, newText: string) => void): void;
};

export function renderTasks(root: HTMLUListElement, tasks: ITask[], handlers: THandlers): void {
    const emptyState = document.querySelector("#empty-state") as HTMLElement | null;

    root.innerHTML = "";

    if (emptyState) {
        if (tasks.length === 0) emptyState.classList.remove("hidden");
        else emptyState.classList.add("hidden");
    }

    for (const task of tasks) {
        const li = document.createElement("li");
        li.classList.add("task");
        if (task.completed) li.classList.add("completed");

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.id = `task-${task.id}`;
        checkbox.checked = task.completed;
        checkbox.addEventListener("change", () => handlers.onToggle(task.id));

        const label = document.createElement("label");
        label.setAttribute("for", checkbox.id);
        label.textContent = task.text;
        label.classList.add("task__label");
        label.title = task.text;

        const actions = document.createElement("div");
        actions.classList.add("task__actions");

        const btnEdit = document.createElement("button");
        btnEdit.classList.add("task__btn", "task__btn-edit");
        btnEdit.append(createElement(Pencil, { "aria-hidden": "true" }), document.createTextNode(" Edit"));

        let isEditing = false;
        btnEdit.addEventListener("click", () => {
            isEditing = !isEditing;

            btnEdit.innerHTML = "";
            btnEdit.append(
                createElement(isEditing ? Check : Pencil, { "aria-hidden": "true" }),
                document.createTextNode(isEditing ? " Save" : " Edit")
            );

            if (isEditing) {
                handlers.onStartInlineEdit(label, task.id, (_id, _newText) => {});
            } else {
                const active = document.activeElement;
                if (active instanceof HTMLInputElement) active.blur();
            }
        });

        const btnDelete = document.createElement("button");
        btnDelete.classList.add("task__btn", "task__btn-delete");
        btnDelete.append(createElement(Trash2, { "aria-hidden": "true" }), document.createTextNode(" Delete"));
        btnDelete.addEventListener("click", () => handlers.onDelete(task.id));

        actions.append(btnEdit, btnDelete);
        li.append(checkbox, label, actions);
        root.appendChild(li);
    }
}
