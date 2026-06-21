function validatePassword() {
  var password = document.getElementById("password");
  var confirm = document.getElementById("confirm_password");

  if (!password || !confirm) return true;

  if (password.value.length < 6) {
    alert("Password must be at least 6 characters long.");
    password.classList.add("error-border");
    return false;
  }

  if (password.value !== confirm.value) {
    alert("Passwords do not match.");
    confirm.classList.add("error-border");
    return false;
  }

  return true;
}

document.addEventListener("DOMContentLoaded", function () {
  var password = document.getElementById("password");
  var confirm = document.getElementById("confirm_password");

  if (password) {
    password.addEventListener("input", function () {
      this.classList.remove("error-border");
    });
  }
  if (confirm) {
    confirm.addEventListener("input", function () {
      this.classList.remove("error-border");
    });
  }

  var phoneInput = document.getElementById("phone");
  if (phoneInput) {
    if (phoneInput.value === "" || phoneInput.value === "+251") {
      phoneInput.value = "+251";
    }
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

  var theme = localStorage.getItem("theme");
  var btn = document.getElementById("theme-btn");
  if(theme === "light"){
    document.documentElement.classList.add("light-mode");
    if(btn) btn.textContent = "🌙 Dark";
  } else {
    document.documentElement.classList.remove("light-mode");
    if(btn) btn.textContent = "☀️ Light";
  }

  if (window.location.hash === '#register') {
    showRegisterSection();
  }
});

function orderMessage() {
  alert("Order Submitted Successfully");
}

function validateReservation() {
  var name = document.getElementById("name");
  var phone = document.getElementById("phone");
  var tableId = document.getElementById("table_id");

  if (name && name.value.trim() === "") {
    name.style.borderColor = "red";
    name.focus();
    return false;
  }

  if (phone) {
    var phonePattern = /^(\+251[79][0-9]{8}|0[79][0-9]{8})$/;
    if (!phonePattern.test(phone.value)) {
      phone.style.borderColor = "red";
      phone.focus();
      alert("Please enter a valid Ethiopian phone number.");
      return false;
    }
  }

  if (tableId && tableId.value === "") {
    alert("Please choose a table.");
    return false;
  }

  return true;
}

function showRegisterSection() {
  var section = document.getElementById("register");
  if (!section) return;
  section.style.display = "block";
  window.location.hash = '#register';
  section.scrollIntoView({ behavior: "smooth", block: "start" });
}

function toggleOccasionNote(value) {
  var noteDiv = document.getElementById("occasion-note");
  var hintText = document.getElementById("occasion-hint-text");

  if (!noteDiv || !hintText) return;

  var hints = {
    birthday:
      "Happy birthday! Tell us if you want a cake, candles, or decorations in special requests.",
    anniversary:
      "Congratulations! Mention flowers, champagne, or a quiet table in special requests.",
    date_night: "We can suggest a quieter table — add any preferences below.",
    business: "Need a private or quiet area? Describe it in special requests.",
    graduation: "Tell us how many are celebrating so we can prepare.",
    other: "Describe your celebration in special requests.",
    none: "",
  };

  if (value !== "none" && hints[value]) {
    hintText.textContent = hints[value];
    noteDiv.style.display = "block";
  } else {
    noteDiv.style.display = "none";
  }
}

function toggleDarkMode(){
  var btn = document.getElementById("theme-btn");
  if(document.documentElement.classList.contains("light-mode")){
    document.documentElement.classList.remove("light-mode");
    localStorage.setItem("theme", "dark");
    if(btn) btn.textContent = "☀️ Light";
  } else {
    document.documentElement.classList.add("light-mode");
    localStorage.setItem("theme", "light");
    if(btn) btn.textContent = "🌙 Dark";
  }
}
