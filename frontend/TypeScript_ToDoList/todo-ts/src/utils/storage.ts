import type { ITask } from "../types/task";
import { isValidTask } from "./validation";

const STORAGE_KEY = "tasks";

export function loadTasks(): ITask[] {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return [];

        const parsed: unknown = JSON.parse(raw);
        if (!Array.isArray(parsed)) return [];

        const valid: ITask[] = [];
        for (const item of parsed) {
            if (isValidTask(item)) {
                valid.push(item);
            } else {
                console.warn("Invalid task discarded:", item);
            }
        }
        return valid;
    } catch (err) {
        console.warn("Error loading tasks:", err);
        return [];
    }
}

export function saveTasks(tasks: ITask[]): void {
    const valid = tasks.filter((t) => {
        const ok = isValidTask(t);
        if (!ok) console.warn("Invalid task discarded (not saved):", t);
        return ok;
    });

    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(valid));
    } catch (err: unknown) {
        const isQuotaError =
            err instanceof DOMException &&
            (err.name === "QuotaExceededError" ||
                err.name === "NS_ERROR_DOM_QUOTA_REACHED" ||
                (err as any).code === 22 ||
                (err as any).code === 1014);

        if (isQuotaError) {
            console.warn("Storage quota exceeded – could not save tasks:", err);
        } else {
            console.warn("Error saving tasks:", err);
        }
    }
}
