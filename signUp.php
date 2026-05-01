<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Registration Form | RallySpot</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* This CSS is moved here for a self-contained solution */
        .home-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #ffffff;
            color: #333;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            z-index: 1000;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .home-btn:hover {
            background-color: #71b7e6;
            color: #fff;
        }
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            background: url("pricepic.jpg") no-repeat center center/cover;
        }
        @media(min-width: 768px) {
            form .user-details .input-box {
                width: calc(100% / 2 - 15px);
            }
        }
        .container {
            max-width: 600px;
            width: 100%;
            background-color: #fff;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-height: 100vh;
        }
        .container .title {
            font-size: 25px;
            font-weight: 500;
            position: relative;
        }
        .container .title::before {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 30px;
            border-radius: 5px;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
        }
        .content form .user-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 20px 0 12px 0;
        }
        form .user-details .input-box {
            margin-bottom: 15px;
            width: calc(100% / 2 - 20px);
        }
        form .input-box span.details {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }
        .user-details .input-box input {
            height: 45px;
            width: 100%;
            outline: none;
            font-size: 16px;
            border-radius: 5px;
            padding-left: 15px;
            border: 1px solid #ccc;
            border-bottom-width: 2px;
            transition: all 0.3s ease;
        }
        .user-details .input-box input:focus,
        .user-details .input-box input:valid {
            border-color: #9b59b6;
            border-radius: 20px;
        }
        form .gender-details .gender-title {
            font-size: 20px;
            font-weight: 500;
        }
        form .category {
            display: flex;
            width: 80%;
            margin: 14px 0;
            justify-content: space-between;
        }
        form .category label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        form .category label .dot {
            height: 18px;
            width: 18px;
            border-radius: 50%;
            margin-right: 10px;
            background: #d9d9d9;
            border: 5px solid transparent;
            transition: all 0.3s ease;
        }
        #dot-1:checked~.category label .one,
        #dot-2:checked~.category label .two {
            background: #9b59b6;
            border-color: #d9d9d9;
        }
        form input[type="radio"] {
            display: none;
        }
        form .button {
            margin: 20px 0 0 0;
        }
        form .button input {
            width: 200px;
            height: 50px;
            border-radius: 6px;
            border: none;
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            letter-spacing: 1px;
            cursor: pointer;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            transition: all 0.3s ease;
            margin-left: 40%;
        }
        form .button input:hover {
            background: linear-gradient(-135deg, #71b7e6, #9b59b6);
        }
        @media(max-width: 584px) {
            .container {
                max-width: 100%;
            }
            form .user-details .input-box {
                margin-bottom: 15px;
                width: 100%;
            }
            form .category {
                width: 100%;
            }
            .content form .user-details {
                max-height: 300px;
                overflow-y: scroll;
            }
            .user-details::-webkit-scrollbar {
                width: 5px;
            }
        }
        @media(max-width: 459px) {
            .container .content .category {
                flex-direction: column;
            }
        }
        /* Hide the actual radio buttons */
form input[type="radio"] {
    display: none;
}

/* Style the custom dots */
form .category label .dot {
    height: 18px;
    width: 18px;
    border-radius: 50%;
    margin-right: 10px;
    background: #d9d9d9;
    border: 5px solid transparent;
    transition: all 0.3s ease;
}

/* Change the dot's color when the associated radio button is checked */
form input[type="radio"]:checked + .dot {
    background: #9b59b6;
    border-color: #d9d9d9;
}
    </style>
</head>
<body>
    <a href="index.php" class="home-btn">Home</a>
    <div class="container">
        <div class="title">Registration</div>
        <div class="content">
            <form id="registerForm" method="post" action="verify_and_insert.php">
                <div class="user-details">
                    <div class="input-box">
                        <span class="details">Full Name</span>
                        <input type="text" name="name" id="name" placeholder="Enter your name" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Date Of Birth</span>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Email</span>
                        <input type="email" name="email" id="email" placeholder="Enter your email" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Phone Number</span>
                        <input type="tel" name="phone" id="phone" maxlength="10" pattern="[0-9]{10}" placeholder="Enter your number" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Password</span>
                        <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    </div>
                    <div class="input-box">
                        <span class="details">Confirm Password</span>
                        <input type="password" id="cpassword" name="cpassword" placeholder="Confirm your password" required>
                    </div>
                </div>
                <div class="gender-details">
                    <span class="gender-title">Gender</span>
                    <div class="category">
                        <label for="dot-1">
                            <input type="radio" name="gender" id="dot-1" value="Male" required>
                            <span class="dot one"></span>
                            <span class="gender">Male</span>
                        </label>
                        <label for="dot-2">
                            <input type="radio" name="gender" id="dot-2" value="Female" required>
                            <span class="dot two"></span>
                            <span class="gender">Female</span>
                        </label>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" value="Register">
                </div>
                <div id="error" style="color: red; margin-top: 10px;"></div>
            </form>
        </div>
    </div>
    <script>
        document.getElementById("registerForm").addEventListener("submit", function(event) {
            let errorDiv = document.getElementById("error");
            errorDiv.innerText = "";
            
            const dobInput = document.getElementById("dob");
            if (dobInput.value) {
                const birthDate = new Date(dobInput.value);
                const currentDate = new Date();
                let age = currentDate.getFullYear() - birthDate.getFullYear();
                const m = currentDate.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && currentDate.getDate() < birthDate.getDate())) {
                    age--;
                }
                if (age < 18) {
                    event.preventDefault();
                    errorDiv.innerText = "❌ You must be at least 18 years old!";
                    return;
                }
            } else {
                 event.preventDefault();
                 errorDiv.innerText = "❌ Please enter a valid Date of Birth!";
                 return;
            }
            
            let email = document.getElementById("email").value;
            if (!email.includes("@gmail.com")) {
                event.preventDefault();
                errorDiv.innerText = "❌ Invalid email! Only @gmail.com is supported.";
                return;
            }
            
            let pass = document.getElementById("password").value.trim();
            let cpass = document.getElementById("cpassword").value.trim();
            if (pass !== cpass) {
                event.preventDefault();
                errorDiv.innerText = "❌ Passwords do not match!";
                return;
            }
        });
    </script>
</body>
</html>
