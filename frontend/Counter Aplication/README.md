# Task 001 — Accessible Counter App (Vite + Vanilla JS)

A simple, accessible counter application built with Vite.

## Overview
This project is part of the Shipsgo Frontend Internship tasks.
It demonstrates a simple counter app with full accessibility (a11y) support,
keyboard navigation, and persistent localStorage state.

## Features
- Accessible counter with `aria-live="polite"`
- Persistent count using `localStorage`
- Keyboard support (Tab, Enter, Space)
- Responsive centered layout
- Hover and focus-visible effects

## Folder Structure
frontend-task/
├─ index.html
├─ src/
│  ├─ main.js
│  └─ style.css
└─ README.md

---

##  Setup and Development

1. Clone the repository
   ```bash
   git clone https://github.com/bawerbozdag/shipsgo-frontend-intern-projects.git
   cd shipsgo-frontend-intern-projects/projects/zehra/task-001

2. Install Dependencies
    npm install

3. Run the development server
    npm run dev

4.	Open the local server URL shown in the terminal (default: http://localhost:5173)


## Test Scenarios

1- Initial value shows "0" 
2- Increase button adds +1 
3- Counter never goes below 0 
4- Reset button sets value to 0 
5- Keyboard works (Tab, Enter, Space)
6- Focus ring visible 
7- Responsive layout intact 
8- Aria-live updates read by screen readers 

## Author

Zehra Karataş
Intern – Shipsgo
October 2025