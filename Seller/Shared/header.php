<header class="page-header">
    <p>Pharmacy Information System v1.1.0.0</p>
    <p>Hello, <span id="employee-name"></span>!</p>
</header>

<script>
    let employee = JSON.parse(localStorage.getItem("employee"));

    if (employee && employee.Fname) {
        document.getElementById("employee-name").textContent = employee.Fname;
    } else {
        document.getElementById("employee-name").textContent = "Guest"; 
    }
</script>
