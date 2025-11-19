import "./style.css";

const STORAGE_KEY = "task001_count";
const MIN_COUNT = 0;
const MAX_COUNT = Number.MAX_SAFE_INTEGER;
// const MAX_COUNT = 10; /* Test */

const countEl = document.querySelector("#count-output");
const controls = document.querySelector("#controls");

let count = loadCount();


renderCount();
updateDisabledStates();


controls.addEventListener("click", (e) => {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;

  const action = btn.dataset.action;
  if (action === "increment") return increment();
  if (action === "decrement") return decrement();
  if (action === "reset") return reset();
});

function increment() {
  if (count >= MAX_COUNT) return;
  count++;
  sync();
}

function decrement() {
  if (count <= MIN_COUNT) return;
  count--;
  sync();
}

function reset() {
  count = 0;
  sync();
}


function sync() {
  saveCount();
  renderCount();
  updateDisabledStates();
}

function renderCount() {
  countEl.textContent = count;
}

function updateDisabledStates() {
  document.querySelector("#btn-decrement").disabled = count <= MIN_COUNT;
  document.querySelector("#btn-increment").disabled = count >= MAX_COUNT;
}

function normalizeCount(value) {
  const n = Number(value);

  if (!Number.isFinite(n)) return 0;         
  const int = Math.trunc(n);                
  if (!Number.isSafeInteger(int)) return 0; 
  if (int < MIN_COUNT) return MIN_COUNT;    
  if (int > MAX_COUNT) return MAX_COUNT;     
  return int;
}
function saveCount() {
  const safe = normalizeCount(count);
  localStorage.setItem(STORAGE_KEY, String(safe));
}

function loadCount() {
  const raw = localStorage.getItem(STORAGE_KEY);

  if (raw === null) return 0;

  const safe = normalizeCount(raw);

  if (String(safe) !== String(raw)) {
    localStorage.setItem(STORAGE_KEY, String(safe));
  }
  return safe;
}