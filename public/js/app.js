function validateRegisterForm() {
  const form = document.getElementById('registerForm');
  const name = form.name.value.trim();
  const email = form.email.value.trim();
  const password = form.password.value;
  const confirm = form.confirm_password.value;

  if (!name || !email || !password || !confirm) {
    alert("All fields are required!");
    return false;
  }
  if (password.length < 6) {
    alert("Password must be at least 6 characters.");
    return false;
  }
  if (password !== confirm) {
    alert("Passwords do not match!");
    return false;
  }
  return true;
}

console.log("JavaScript loaded successfully!");
<script src="js/app.js"></script>
