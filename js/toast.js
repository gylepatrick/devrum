function showToast(message, type = "success") {
  const toastEl = document.getElementById("toast");
  const toastBody = toastEl.querySelector(".toast-body");

  toastEl.classList.remove("bg-success", "bg-danger");
  toastEl.classList.add(type === "success" ? "bg-success" : "bg-danger", "text-white");

  toastBody.textContent = message;

  new bootstrap.Toast(toastEl).show();
}
