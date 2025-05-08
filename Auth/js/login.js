const togglePassword = document.querySelector("#togglePassword");
const password = document.querySelector("#password");

togglePassword.addEventListener("click", function (e) {
  const type = password.getAttribute("type") === "password" ? "text" : "password";
  password.setAttribute("type", type);
  this.classList.toggle("img/hidden.png");
});

document
  .querySelector(".login-form")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    const username = document.querySelector("#username").value;
    const password = document.querySelector("#password").value;

    const formData = new FormData();
    formData.append("username", username);
    formData.append("password", password);

    fetch("login-action.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json()) // Expecting JSON response
      .then((data) => {
        if (data.success) {
          localStorage.setItem("user", JSON.stringify(data.user));
          localStorage.setItem("employee", JSON.stringify(data.employee));
          localStorage.setItem("usertype", data.usertype);          

          window.history.pushState(null, '', window.location.href); 
          window.history.replaceState(null, '', window.location.href);

          window.location.href = data.redirectUrl;
        } else {
          document.getElementById("error").textContent = data.message || "";
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        document.getElementById("error").textContent = "There was an error with the login. Please try again.";
      });
  });

  document.addEventListener("DOMContentLoaded", function(){
    localStorage.clear();
  });

  document.addEventListener("contextmenu", function(e) {
    e.preventDefault(); 
});

document.addEventListener("keydown", function(e) {
    if (e.key === "F12") {
        e.preventDefault();
    }

    if (e.ctrlKey && e.key.toLowerCase() === "u") {
        e.preventDefault();
    }

    if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === "i") {
        e.preventDefault();
    }
});
