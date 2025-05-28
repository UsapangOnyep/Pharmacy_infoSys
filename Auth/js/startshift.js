document.addEventListener("DOMContentLoaded", function () {
    const startShiftButton = document.getElementById("startShiftButton");

    startShiftButton.addEventListener("click", function () {
        const user = JSON.parse(localStorage.getItem("user"));

        console.log("User data:", user); // Debugging line to check user data

        if (!user || !user.ID) {
            alert("User not logged in.");
            return;
        }

        const formData = new FormData();
        formData.append("action", "startShift");
        formData.append("user_id", user.ID);

        fetch("start-shift-action.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    //sweet alert or similar notification can be used here
                    localStorage.setItem("shiftData", JSON.stringify(data.shiftData));

                    alert("Shift started successfully!");
                    if (data.redirectUrl) {
                        window.location.href = data.redirectUrl;
                    }
                } else {
                    alert(data.message || "Failed to start shift.");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("There was an error starting the shift. Please try again.");
            });
    });
});


document.addEventListener("contextmenu", function(e) {
    e.preventDefault(); // Prevent right-click context menu
});

document.addEventListener("keydown", function(e) {
    if (e.key === "F12") {
        e.preventDefault(); // Prevent F12 key
    }

    if (e.ctrlKey && e.key.toLowerCase() === "u") {
        e.preventDefault(); // Prevent Ctrl+U
    }

    if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === "i") {
        e.preventDefault(); // Prevent Ctrl+Shift+I
    }
});

// check if the user is logged in and if the shift is already started
document.addEventListener("DOMContentLoaded", function() {
    const user = localStorage.getItem("user");
    if (!user) {
        window.location.href = "login.php"; // Redirect to login if not logged in
    } else {
        // Check if shift is already started
        fetch("check-shift-status.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ user_id: JSON.parse(user).ID })
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success && data.shiftStatus === "started") {
                alert("Shift is already started.");
                window.location.href = "dashboard.php"; // Redirect to dashboard if shift is already started
            }
        })
        .catch((error) => {
            console.error("Error:", error);
        });
    }
});