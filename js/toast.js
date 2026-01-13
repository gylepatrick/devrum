/**
 * Show Toast Notification
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, warning, info)
 * @param {number} duration - Duration in milliseconds (default: 5000)
 */
function showToast(message, type = "success", duration = 5000) {
  // Create toast container if it doesn't exist
  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container position-fixed bottom-0 end-0 p-3";
    document.body.appendChild(container);
  }

  // Create toast element
  const toastEl = document.createElement("div");
  toastEl.className = `toast show ${type}`;
  toastEl.setAttribute("role", "alert");
  toastEl.setAttribute("aria-live", "assertive");
  toastEl.setAttribute("aria-atomic", "true");

  // Map types to icons and colors
  const typeConfig = {
    success: { icon: "✓", color: "#10b981" },
    error: { icon: "✕", color: "#ef4444" },
    warning: { icon: "!", color: "#f59e0b" },
    info: { icon: "ℹ", color: "#0ea5e9" },
  };

  const config = typeConfig[type] || typeConfig.success;

  // Create toast content
  toastEl.innerHTML = `
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div style="font-size: 1.25rem; color: ${config.color}; font-weight: bold;">
        ${config.icon}
      </div>
      <div class="toast-body" style="padding: 0; margin: 0;">
        ${message}
      </div>
      <button type="button" class="btn-close btn-sm ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;

  // Add to container
  container.appendChild(toastEl);

  // Auto-remove after duration
  setTimeout(() => {
    toastEl.classList.remove("show");
    setTimeout(() => {
      toastEl.remove();
    }, 150);
  }, duration);
}

// Legacy support - keep old API
function showToastBootstrap(message, type = "success") {
  showToast(message, type === "success" ? "success" : "error");
}

