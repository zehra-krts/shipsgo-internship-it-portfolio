import type { ITask } from "../../types/task";
import { getList, getInput, getButton } from "../../utils/dom";
import { loadTasks, saveTasks } from "../../utils/storage";
import { showToast } from "../../utils/toast";
import { renderTasks } from "./render";
import { inlineEdit } from "./inline-edit";

const MAX_LENGTH = 100;

let tasks: ITask[] = [];
let currentFilter: "all" | "active" | "completed" = "all";

let taskListEl!: HTMLUListElement;
let taskInputEl!: HTMLInputElement;
let addTaskBtnEl!: HTMLButtonElement;
let charCountEl!: HTMLElement;

export function bootstrap(): void {
    tasks = loadTasks();
    taskListEl = getList("#task-list");
    taskInputEl = getInput("#new-task");
    addTaskBtnEl = getButton("#btn-add");
    charCountEl = document.querySelector("#char-count") as HTMLElement;

    bindGlobalEvents();
    render();
}

function bindGlobalEvents(): void {
    addTaskBtnEl.addEventListener("click", onAddClick);
    taskInputEl.addEventListener("keydown", (event: KeyboardEvent) => {
        if (event.key === "Enter") onAddClick();
    });

    document.querySelectorAll("[data-filter]").forEach((el) => {
        if (el instanceof HTMLButtonElement) el.addEventListener("click", onFilter);
    });

    if (charCountEl) {
        const updateCounter = () => {
            const len = taskInputEl.value.length;
            charCountEl.textContent = `${len} / ${MAX_LENGTH}`;
            charCountEl.classList.toggle("limit-exceeded", len > MAX_LENGTH);
        };

        taskInputEl.addEventListener("input", updateCounter);
        updateCounter();
    }
}

function onFilter(e: Event): void {
    const button = e.currentTarget;
    if (!(button instanceof HTMLButtonElement)) return;

    const value = button.dataset.filter;
    if (value === "all" || value === "active" || value === "completed") {
        currentFilter = value;

        document.querySelectorAll("[data-filter]").forEach((el) => {
            if (el instanceof HTMLButtonElement) {
                const isActive = el === button;
                el.classList.toggle("is-active", isActive);
                el.setAttribute("aria-pressed", String(isActive));
            }
        });

        render();
    }
}

function onAddClick(): void {
    const text = taskInputEl.value.trim();

    if (!text) {
        showToast("Please enter a task before adding!");
        taskInputEl.focus();
        return;
    }

    if (text.length > MAX_LENGTH) {
        showToast(`Task text is too long! Max ${MAX_LENGTH} characters.`);
        taskInputEl.focus();
        return;
    }

    tasks.push({ id: Date.now(), text, completed: false });
    persistAndRender();

    taskInputEl.value = "";
    if (charCountEl) {
        charCountEl.textContent = `0 / ${MAX_LENGTH}`;
        charCountEl.classList.remove("limit-exceeded");
    }
    taskInputEl.focus();
}

function onToggle(id: number): void {
    const task = tasks.find((item) => item.id === id);
    if (!task) return;
    task.completed = !task.completed;
    persistAndRender();
}

function onEdit(id: number, newText: string): void {
    const task = tasks.find((t) => t.id === id);
    if (!task) return;

    const trimmed = newText.trim();

    if (!trimmed) {
        showToast("Task cannot be empty!");
        render();
        return;
    }

    if (trimmed.length > MAX_LENGTH) {
        showToast(`Task text is too long! Max ${MAX_LENGTH} characters.`);
        render();
        return;
    }

    task.text = trimmed;
    persistAndRender();
}

function onDelete(id: number): void {
    tasks = tasks.filter((item) => item.id !== id);
    persistAndRender();
}

function persistAndRender(): void {
    saveTasks(tasks);
    render();
}

function render(): void {
    const visible = tasks.filter((task) => {
        if (currentFilter === "active") return !task.completed;
        if (currentFilter === "completed") return task.completed;
        return true;
    });

    renderTasks(taskListEl, visible, {
        onToggle,
        onDelete,
        onStartInlineEdit(labelEl, taskId, _commitFromRender) {
            inlineEdit(
                labelEl,
                taskId,
                (id, updatedText) => {
                    onEdit(id, updatedText);
                    render();
                },
                () => render()
            );
        },
    });
}
