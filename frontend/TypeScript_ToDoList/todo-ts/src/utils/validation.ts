import type { ITask } from "../types/task";

export function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === "object" && value !== null;
}

export function isValidTask(value: unknown): value is ITask {
    if (!isRecord(value)) return false;

    const { id, text, completed } = value;

    const okId = typeof id === "number";
    const okText = typeof text === "string" && text.trim().length > 0;
    const okCompleted = typeof completed === "boolean";

    return okId && okText && okCompleted;
}

export function normalizeTitle(input: string): string {
    return input.trim().replace(/\s+/g, " ");
}
