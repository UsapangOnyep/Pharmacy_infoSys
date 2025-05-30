document.addEventListener("DOMContentLoaded", function () {
    const startShiftButton = document.getElementById("startShiftButton");

    startShiftButton.addEventListener("click", function () {
        const user = JSON.parse(localStorage.getItem("user"));

        console.log("User data:", user); // Debugging line to check user data

        if (!user || !user.ID) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'You must be logged in to start a shift.',
                confirmButtonText: 'OK'
            });
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

                    Swal.fire({
                        icon: 'success',
                        title: 'Shift Started',
                        text: 'Your shift has been started successfully.',
                        confirmButtonText: 'OK'
                    });

                    if (data.redirectUrl) {
                        window.location.href = data.redirectUrl;
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to start shift.',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "There was an error starting the shift. Please try again.",
                    confirmButtonText: 'OK'
                });
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
document.addEventListener("DOMContentLoaded", function () {
    const user = JSON.parse(localStorage.getItem("user"));

    if (!user) {
        window.location.href = "login.php"; // Redirect to login if not logged in
        return;
    }

    // Check if shift is already started
    const formData = new FormData();
    formData.append("user_id", user.ID);
    formData.append("action", "checkShiftStatus");

    console.log("Checking shift status for user ID:", user.ID);

    fetch("check-shift-status.php", {
        method: "POST",
        body: formData
    })
    .then((response) => response.json())
    .then((data) => {
        console.log("Shift status response:", data);

        if (data.success === false && data.shiftData) {
            // User already has an active shift
            localStorage.setItem("shiftData", JSON.stringify(data.shiftData));

            Swal.fire({
                icon: 'info',
                title: 'Shift Active',
                text: 'You already have an active shift.',
                confirmButtonText: 'OK'
            });

            if (data.redirectUrl) {
                window.location.href = data.redirectUrl;
            }
        } else if (data.success === true) {
            // No shift found (may proceed to create one)
            Swal.fire({
                icon: 'info',
                title: 'No Active Shift',
                text: 'You can start a new shift.',
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "Unexpected response. Please try again.",
                confirmButtonText: 'OK'
            });
        }
    })
    .catch((error) => {
        console.error("Error:", error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "An error occurred while checking shift status.",
            confirmButtonText: 'OK'
        });
    });
});
