<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do with Voice funtionality</title>
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
            overflow: hidden;
            /* Prevent unwanted scrolling */
        }

        .container {
            display: flex;
            width: 800px;
            padding: 20px;
            max-height: 80vh;
            /* Prevent stretching */
            background: rgba(255, 255, 255, 0.1);
            /* Semi-transparent */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            backdrop-filter: blur(10px);
            /* Adds a frosted glass effect */
        }

        .form-container {
            flex: 1;
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* Center content */
        }

        img {
            max-width: 100%;
            height: auto;
            /* Prevent image from overflowing */
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }


        h1 {
            margin-bottom: 10px;
        }

        p {
            margin-bottom: 20px;
            color: #666;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background: rgb(58, 91, 34);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0056b3;
        }

        .switch {
            margin-top: 10px;
            color: #ffff;
        }

        .switch a {
            color: #007BFF;
            text-decoration: none;
        }

        .switch a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Left Section (Login Form) -->  
        <div class="form-container">
            <h1>Welcome Back!</h1>
            <p>Login to continue</p>
            <form id="loginForm" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button class="login-btn" type="submit">Login</button>
            </form>
            <p class="switch">Don't have an account? <a href="SignupPage.php">Sign Up</a></p>
        </div>

        <!-- Right Section (Image Section) -->
        <div class="form-container">
            <img src="Images/chris-lee-70l1tDAI6rM-unsplash 1.png" alt="">
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            try {
                const response = await fetch('http://localhost/VocalToDo/backend/login.php', {
                    method: 'POST',
                    headers: {
                'Accept': 'application/json',
            },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Redirect on successful login
                    window.location.href = 'dashboard.php';
                } else {
                    // Show error message
                    alert(data.message || 'Login failed. Please try again.');
                }
            } catch (error) {
                console.error('Login error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    </script>
</body>
</html>