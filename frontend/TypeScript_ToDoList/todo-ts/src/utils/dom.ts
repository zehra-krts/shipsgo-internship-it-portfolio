export function getElement(selector: string): HTMLElement {
    const el = document.querySelector(selector);
    if (!(el instanceof HTMLElement)) {
        throw new Error(`Element not found or not an HTMLElement: ${selector}`);
    }
    return el;
}

export function getInput(selector: string): HTMLInputElement {
    const el = document.querySelector(selector);
    if (!(el instanceof HTMLInputElement)) {
        throw new Error(`Element is not an <input>: ${selector}`);
    }
    return el;
}

export function getButton(selector: string): HTMLButtonElement {
    const el = document.querySelector(selector);
    if (!(el instanceof HTMLButtonElement)) {
        throw new Error(`Element is not a <button>: ${selector}`);
    }
    return el;
}

export function getList(selector: string): HTMLUListElement {
    const el = document.querySelector(selector);
    if (!(el instanceof HTMLUListElement)) {
        throw new Error(`Element is not a <ul>: ${selector}`);
    }
    return el;
}
