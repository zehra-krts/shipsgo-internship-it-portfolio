import "./style.css";
import { createElement, Trash2, Pencil, Check } from "lucide";

const STORAGE_KEY = "tasks";
let tasks = [];
let toastTimer;

const input = document.querySelector("#new-task");
const addButton = document.querySelector("#btn-add");
const taskList = document.querySelector("#task-list");

bootstrap();

function bootstrap() {
    loadTasks();
    renderTasks();
    bindGlobalEvents();
}
function bindGlobalEvents() {
    addButton.addEventListener("click", onAddClick);
    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter") onAddClick();
    });
}

function onAddClick() {
    const text = input.value.trim();
    if (text === "") {
        showToast("Please enter a task before adding!");
        input.focus();
        return;
    }

    const newTask = { id: Date.now(), text, completed: false };
    tasks.push(newTask);

    saveTasks();
    renderTasks();

    input.value = "";
    input.focus();
}
function onToggle(id) {
    const t = tasks.find((x) => x.id === id);
    if (!t) return;
    t.completed = !t.completed;
    saveTasks();
    renderTasks();
}
function onEdit(id, newText) {
    const t = tasks.find((x) => x.id === id);
    if (!t) return;
    t.text = newText.trim();
    saveTasks();
    renderTasks();
}
function onDelete(id) {
    tasks = tasks.filter((x) => x.id !== id);
    saveTasks();
    renderTasks();
}

function showToast(msg) {
    const el = document.querySelector("#toast");
    if (!el) return;

    el.textContent = msg;
    el.classList.remove("hidden");

    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        el.classList.add("hidden");
        toastTimer = null;
    }, 4000);
}
function loadTasks() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) {
            tasks = [];
            return;
        }
        const parsed = JSON.parse(raw);
        if (!Array.isArray(parsed)) throw new Error("Not an array");

        const ok = parsed.every(
            (t) =>
                t &&
                typeof t === "object" &&
                typeof t.id === "number" &&
                typeof t.text === "string" &&
                typeof t.completed === "boolean"
        );
        if (!ok) throw new Error("Invalid task shape");

        tasks = parsed;
    } catch (err) {
        tasks = [];
        showToast("Data could not be loaded, starting with a new list.");
    }
}

function saveTasks() {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(tasks));
    } catch (err) {
        showToast("Changes could not be saved. Storage may be full.");
    }
}

function renderTasks() {
    const emptyState = document.querySelector("#empty-state");
    taskList.innerHTML = "";

    if (tasks.length === 0) {
        emptyState.classList.remove("hidden");
    } else {
        emptyState.classList.add("hidden");
    }

    for (const task of tasks) {
        const li = document.createElement("li");
        li.classList.add("task");
        if (task.completed) li.classList.add("completed");

        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.id = `task-${task.id}`;
        checkbox.checked = task.completed;
        checkbox.addEventListener("change", () => onToggle(task.id));

        const label = document.createElement("label");
        label.setAttribute("for", checkbox.id);
        label.textContent = task.text;
        label.classList.add("task-label");

        const actions = document.createElement("div");
        actions.classList.add("task__actions");

        const btnEdit = document.createElement("button");
        btnEdit.classList.add("task__btn-edit");
        btnEdit.setAttribute("aria-label", "Edit task");

        const editIcon = createElement(Pencil, { "aria-hidden": true });
        const editText = document.createTextNode(" Edit");
        btnEdit.append(editIcon, editText);

        btnEdit.addEventListener("click", () => {
            const isEditing = btnEdit.classList.toggle("editing");
            btnEdit.innerHTML = "";

            const newIcon = createElement(isEditing ? Check : Pencil, { "aria-hidden": true });
            const newText = document.createTextNode(isEditing ? " Save" : " Edit");

            btnEdit.append(newIcon, newText);

            if (isEditing) {
                inlineEdit(label, task.id);
            }
        });

        const btnDelete = document.createElement("button");
        btnDelete.classList.add("task__btn-delete");
        btnDelete.setAttribute("aria-label", "Delete task");

        const deleteIcon = createElement(Trash2, { "aria-hidden": true });
        const deleteText = document.createTextNode(" Delete");
        btnDelete.append(deleteIcon, deleteText);

        btnDelete.addEventListener("click", () => onDelete(task.id));

        actions.append(btnEdit, btnDelete);
        li.append(checkbox, label, actions);
        taskList.appendChild(li);
    }
}
function inlineEdit(labelEl, taskId) {
    const old = labelEl.textContent;
    const inputEl = document.createElement("input");
    inputEl.type = "text";
    inputEl.value = old;

    labelEl.replaceWith(inputEl);
    inputEl.focus();

    const commit = () => {
        const nv = inputEl.value.trim();
        if (nv === "") {
            showToast("Task cannot be empty!");
            inputEl.replaceWith(labelEl);
            return;
        }

        if (nv !== old) {
            onEdit(taskId, nv);
        } else {
            renderTasks();
        }
    };

    inputEl.addEventListener("keydown", (e) => {
        if (e.key === "Enter") commit();
        if (e.key === "Escape") renderTasks();
    });
    inputEl.addEventListener("blur", commit);
}
