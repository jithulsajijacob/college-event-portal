// ---------- Registration Form Validation ----------
function validateRegisterForm() {
  const form = document.getElementById('registerForm');
  const name = form.name.value.trim();
  const email = form.email.value.trim();
  const password = form.password.value;
  const confirm = form.confirm_password.value;

  if (!name || !email || !password || !confirm) {
    alert("⚠️ All fields are required!");
    return false;
  }
  if (password.length < 6) {
    alert("⚠️ Password must be at least 6 characters.");
    return false;
  }
  if (password !== confirm) {
    alert("⚠️ Passwords do not match!");
    return false;
  }
  return true;
}

console.log("✅ JavaScript loaded successfully!");

// ---------- AJAX Event Registration ----------
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.btn-register').forEach(btn => {
    btn.addEventListener('click', function() {
      const eventId = this.dataset.eventId;

      fetch('register_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event_id: eventId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("✅ " + data.message);
        } else {
          alert("⚠️ " + data.error);
        }
      })
      .catch(() => alert("❌ Network error"));
    });
  });
});
