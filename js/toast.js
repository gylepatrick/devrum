function showToast(message) {
  const toastEl = document.getElementById("toast");
  const toastBody = toastEl.querySelector(".toast-body");

  toastBody.textContent = message;

  new bootstrap.Toast(toastEl).show();
}
