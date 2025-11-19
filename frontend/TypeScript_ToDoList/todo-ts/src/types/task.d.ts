export type TTaskStatus = "active" | "completed";

export interface ITask {
    id: number;
    text: string;
    completed: boolean;
}
