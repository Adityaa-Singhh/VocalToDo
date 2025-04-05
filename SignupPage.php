<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, rgb(13, 25, 8), rgb(25, 45, 34), rgb(50, 90, 50));
        }

        .container {
            display: flex;
            width: 800px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .form-container {
            flex: 1;
            padding: 20px;
        }

        img {
            max-width: 100%;
            height: auto;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        h1 {
            margin-bottom: 10px;
            color: #fff;
        }

        label {
            font-weight: 600;
            font-size: 16px;
            color: #fff;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        button {
            width: 100%;
            padding: 10px;
            background: rgb(58, 91, 34);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .switch {
            margin-top: 10px;
            color: #fff;
        }

        .switch a {
            color: #007BFF;
            text-decoration: none;
        }

        .switch a:hover {
            text-decoration: underline;
        }
        /* Style the checkbox container */
.checkbox-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
    margin-bottom: 10px;
}

/* Hide the default checkbox */
.checkbox-container input[type="checkbox"] {
    display: none;
}

/* Custom circular checkbox */
.checkbox-container label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

/* Create the circle */
.checkbox-container label::before {
    content: "";
    width: 18px;
    height: 18px;
    border: 2px solid white;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
    transition: 0.3s;
}

/* When checkbox is checked */
.checkbox-container input[type="checkbox"]:checked + label::before {
    background-color: #007BFF;
    border: 2px solid rgb(58, 91, 34);
}

    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h1>Get Started Now</h1>

            <form id="signupForm">
                <label for="name">Name</label>
                <input type="text" name="username" placeholder="Enter your name" id="name" required>

                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Enter your email" id="email" required>

                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Password" id="password" required>

                <div class="checkbox-container">
    <input type="checkbox" id="terms" required>
    <label for="terms">I agree to the Terms & Conditions</label>
</div>


                <button type="submit">Signup</button>
            </form>

            <p class="switch">Have an account? <a href="LoginPage.php">Sign In</a></p>
        </div>
        <div class="form-container">
        <img src="Images/chris-lee-70l1tDAI6rM-unsplash 1.png" alt="">
        </div>
    </div>


<script>
document.getElementById('signupForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    try {
        const formData = new FormData(this);
        const response = await fetch('http://localhost/VocalToDo/backend/signup.php', {
            method: 'POST',
            body: formData
        });

        // First get the response as text
        const responseText = await response.text();
        
        try {
            // Try to parse as JSON
            const data = JSON.parse(responseText);
            
            if (data.success) {
                // Show brief message then redirect immediately
                const message = document.createElement('div');
                message.style.position = 'fixed';
                message.style.top = '20px';
                message.style.left = '50%';
                message.style.transform = 'translateX(-50%)';
                message.style.backgroundColor = 'rgba(58, 91, 34, 0.9)';
                message.style.color = 'white';
                message.style.padding = '10px 20px';
                message.style.borderRadius = '5px';
                message.style.zIndex = '1000';
                message.textContent = 'Signup successful! Redirecting...';
                document.body.appendChild(message);
                
                // Redirect after 1.5 seconds (1500 milliseconds)
                setTimeout(() => {
                    window.location.href = "LoginPage.php";
                }, 1500);
                
                // Remove message after redirect
                setTimeout(() => {
                    document.body.removeChild(message);
                }, 1500);
            } else {
                alert(data.message || 'Signup failed. Please try again.');
            }
        } catch (e) {
            // If not valid JSON, show the raw response
            console.error('Failed to parse JSON:', responseText);
            alert('Server response: ' + responseText);
        }
    } catch (error) {
        console.error('Network error:', error);
        alert('Network error. Please try again.');
    }
});
</script>
</body>
</html>