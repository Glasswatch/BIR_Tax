function hasLowerCase(str) {
    return /[a-z]/.test(str);
}

function hasUpperCase(str) {
    return /[A-Z]/.test(str);
}

function hasNumber(str) {
    return /\d/.test(str);
}

function hasUniqueChar(str) {
    return /[\W_]/.test(str); // non-alphanumeric (special) character
}

function containsNameOrReverse(password, firstName, lastName) {
    const pwd = password.toLowerCase();
    const first = firstName.toLowerCase();
    const last = lastName.toLowerCase();
    const revFirst = first.split('').reverse().join('');
    const revLast = last.split('').reverse().join('');

    return (
        pwd.includes(first) ||
        pwd.includes(last) ||
        pwd.includes(revFirst) ||
        pwd.includes(revLast)
    );
}

function updatePasswordFeedback() {
    const password = document.getElementById('password').value;
    const firstName = document.querySelector('input[name="first_name"]').value;
    const lastName = document.querySelector('input[name="last_name"]').value;

    updateRequirementStatus('length', password.length >= 8);
    updateRequirementStatus('lowercase', hasLowerCase(password));
    updateRequirementStatus('uppercase', hasUpperCase(password));
    updateRequirementStatus('number', hasNumber(password));
    updateRequirementStatus('Unique', hasUniqueChar(password));


}

function updateRequirementStatus(id, isValid) {
    const element = document.getElementById(id);
    if (element) {
        element.classList.toggle('valid', isValid);
    
        element.textContent = (isValid ? '✔️' : '❌') + element.textContent.slice(1);
    }
}

function validatePasswords() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const firstName = document.querySelector('input[name="first_name"]').value;
    const lastName = document.querySelector('input[name="last_name"]').value;

    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return false;
    }

    if (password.length < 8) {
        alert("Password must be at least 8 characters.");
        return false;
    }

    if (!hasLowerCase(password)) {
        alert("Password must contain at least one lowercase letter.");
        return false;
    }

    if (!hasUpperCase(password)) {
        alert("Password must contain at least one uppercase letter.");
        return false;
    }

    if (!hasNumber(password)) {
        alert("Password must contain at least one number.");
        return false;
    }

    if (!hasUniqueChar(password)) {
        alert("Password must contain at least one unique (special) character.");
        return false;
    }

    if (containsNameOrReverse(password, firstName, lastName)) {
        alert("Password must NOT contain your first name, last name, or their reversed versions.");
        return false;
    }

    return true;
}

    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');

    if (error) {
      const modal = document.getElementById("errorModal");
      const message = document.getElementById("modalMessage");
      message.textContent = decodeURIComponent(error.replace(/\+/g, ' '));
      modal.style.display = "block";
    }

    document.querySelector(".close").onclick = () => {
      document.getElementById("errorModal").style.display = "none";
    };

    window.onclick = function (event) {
      const modal = document.getElementById("errorModal");
      if (event.target === modal) {
        modal.style.display = "none";
      }
    };