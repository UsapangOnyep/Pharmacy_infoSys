const usertype = localStorage.getItem("usertype");
const shift = localStorage.getItem("shiftData");

if (usertype === "admin") {
  if (!shift) {
    window.location.href = '../Auth/startshift.php'; 
  }
} else if (usertype === "user") {
  if (!shift) {
    window.location.href = '../Auth/startshift.php'; 
  }
  window.location.href = 'Seller/pos.php'; 
} else {
    window.location.href = '../Auth/';
}

window.history.pushState(null, '', window.location.href); 
window.history.replaceState(null, '', window.location.href);
