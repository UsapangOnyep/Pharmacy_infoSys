const usertype = localStorage.getItem("usertype");

if (usertype === "admin") {
} else if (usertype === "user") {
  window.location.href = 'Seller/pos.php'; 
} else {
    window.location.href = '../Auth/';
}

window.history.pushState(null, '', window.location.href); 
window.history.replaceState(null, '', window.location.href);
