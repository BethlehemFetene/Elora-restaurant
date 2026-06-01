function validatePassword() {
  var password = document.getElementById("password").value;
  var confirm = document.getElementById("confirm_password").value;

  if (password.length < 6) {
    alert("Password must be at least 6 characters long.");
    document.getElementById("password").classList.add("error-border");
    return false;
  }

  if (password !== confirm) {
    alert("Passwords do not match.");
    document.getElementById("confirm_password").classList.add("error-border");
    return false;
  }

  return true;
}

// Remove red border when user starts typing
document.getElementById("password").addEventListener("input", function () {
  this.classList.remove("error-border");
});
document
  .getElementById("confirm_password")
  .addEventListener("input", function () {
    this.classList.remove("error-border");
  });
function orderMessage() {
  alert("Order Submitted Successfully");
}

function validateReservation() {
  let name = document.getElementById("name").value;
  let phone = document.getElementById("phone").value;

  if (name.trim() === "") {
    document.getElementById("name").style.borderColor = "red";
    return false;
  }

  let phonePattern = /^\+251[79][0-9]{8}$/;
  if (!phonePattern.test(phone)) {
    document.getElementById("phone").style.borderColor = "red";
    return false;
  }

  return true;
}

document.addEventListener("DOMContentLoaded", function () {
  let phoneInput = document.getElementById("phone");
  if (phoneInput) {
    phoneInput.value = "+251";
    phoneInput.addEventListener("keydown", function (e) {
      if (
        this.selectionStart <= 4 &&
        (e.key === "Backspace" || e.key === "Delete")
      ) {
        e.preventDefault();
      }
    });
    phoneInput.addEventListener("input", function () {
      if (!this.value.startsWith("+251")) {
        this.value = "+251";
      }
    });
  }
});

function toggleDarkMode() {
  document.body.classList.toggle("dark");
  let btn = document.getElementById("theme-btn");
  if (document.body.classList.contains("dark")) {
    localStorage.setItem("theme", "dark");
    btn.textContent = "☀️ Light";
  } else {
    localStorage.setItem("theme", "light");
    btn.textContent = "🌙 Dark";
  }
}

(function () {
  document.addEventListener("DOMContentLoaded", function () {
    let theme = localStorage.getItem("theme");
    let btn = document.getElementById("theme-btn");
    if (theme === "dark") {
      document.body.classList.add("dark");
      if (btn) btn.textContent = "☀️ Light";
    }
  });
})();
