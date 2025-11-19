let toastTimer: number | null = null;

export function showToast(msg: string): void {
    const el = document.querySelector("#toast") as HTMLElement | null;
    if (!el) return;

    el.textContent = "";
    void el.offsetWidth;
    el.textContent = msg;

    el.classList.remove("hidden");

    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => {
        el.classList.add("hidden");
        toastTimer = null;
    }, 4000);
}
