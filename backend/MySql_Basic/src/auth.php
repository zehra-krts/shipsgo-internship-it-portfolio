<?php
function require_login(): void {
  if (empty($_SESSION['user'])) { header('Location: /login.php'); exit; }
}
function is_admin(): bool {
  return (($_SESSION['user']['role'] ?? '') === 'admin');
}
function require_admin(): void {
  if (!is_admin()) { http_response_code(403); exit('Forbidden'); }
}
function current_user_id(): ?int {
  return $_SESSION['user']['id'] ?? null;
}