// Prevent back button navigation and clear localStorage on page unload
window.history.pushState(null, "", window.location.href);
window.history.replaceState(null, "", window.location.href);

window.addEventListener("DOMContentLoaded", function () {
    const usertype = localStorage.getItem("usertype");

    if (usertype !== "admin" && usertype !== "seller") {
        localStorage.clear();
        window.location.href = "../Auth/";
    }

    window.history.pushState(null, "", window.location.href);
    window.history.replaceState(null, "", window.location.href);
});

window.addEventListener("popstate", function (event) {
  window.history.pushState(null, "", window.location.href); // Prevent going back
});
window.addEventListener("load", function () {
  window.history.pushState(null, "", window.location.href); // Prevent going back on load
});
