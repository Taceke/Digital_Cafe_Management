document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    
    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const username = document.getElementById("username").value.trim();
        const password = document.getElementById("password").value.trim();
        const errorMessage = document.getElementById("error-message");
        
        if (username === "" || password === "") {
            errorMessage.textContent = "Both fields are required.";
            return;
        }
        
        const formData = new FormData(loginForm);

        fetch("login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "dashboard.php";
            } else {
                errorMessage.textContent = data.message;
            }
        })
        .catch(error => {
            console.error("Error:", error);
            errorMessage.textContent = "An error occurred. Please try again.";
        });
    });

    document.getElementById("forgot-password").addEventListener("click", function () {
        alert("Forgot password functionality is under development!");
    });
});
