<?php
session_start();

// Check if the user is logged in. If not, redirect to the login page.
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RallySpot - Home</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #e0f2fe; 
        }
        .text-gradient {
            background-image: linear-gradient(to right, #6366f1, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .container-shadow {
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

       

#bubble-container { 
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1000;
}

.star {
    position: absolute;
    width: 25px; /* Base size for the star */
    height: 25px;
    background-color: transparent; /* Color will be set by JS */
    opacity: 0;
    
    /* === THIS IS THE KEY FOR THE STAR SHAPE === */
    clip-path: polygon(
        50% 0%, 
        61% 35%, 
        98% 35%, 
        68% 57%, 
        79% 91%, 
        50% 70%, 
        21% 91%, 
        32% 57%, 
        2% 35%, 
        39% 35%
    );
    /* ========================================= */

    animation: starAnimation 4s ease-out forwards; /* Apply the star animation */
}

@keyframes starAnimation {
    0% {
        transform: translate3d(0, 0, 0) scale(0) rotate(0deg); /* Start small */
        opacity: 0;
    }
    10% {
        transform: translate3d(0, 0, 0) scale(0.8) rotate(30deg); /* Grow and rotate slightly */
        opacity: 1; /* Become fully visible */
    }
    100% {
        transform: translate3d(0, -200px, 0) scale(1.2) rotate(360deg); /* Rise, grow, and complete a full rotation */
        opacity: 0; /* Fade out */
    }
}
    </style>
</head>
   <div id="bubble-container"></div>
<body class="bg-gray-100 min-h-screen">
    <!-- Main container with a dark background and some padding -->
    <div class="relative bg-white container-shadow rounded-3xl mx-auto my-8 p-6 sm:p-8 lg:p-12 max-w-7xl">
        <!-- Navigation Bar -->
        <nav class="flex items-center justify-between flex-wrap bg-white p-6 rounded-3xl">
            <!-- Logo and Site Name -->
            <div class="flex items-center flex-shrink-0 text-white mr-6">
                <img src="images/logo.jpg" alt="Logo" width="80" height="30" class="d-inline-block align-text-top"></a>
            </div>

            <!-- User Info and Logout Section -->
            <div class="block lg:flex lg:items-center w-auto">
                <!-- User Name Display -->
                <div class="text-sm font-semibold text-gray-800 hidden lg:block">
                    <!-- The PHP code below displays the user's name from the session -->
                    <span id="user-name">Hello, <?php echo htmlspecialchars(explode(" ",$_SESSION['name'])[0]); ?>!</span>
                </div>
                
                <!-- Logout Button -->
                <a href="logout.php" class="lg:ml-4 inline-block text-sm px-4 py-2 leading-none border rounded text-white bg-blue-500 border-blue-500 hover:bg-blue-600 hover:border-blue-600 mt-4 lg:mt-0 transition-colors duration-300">Logout</a>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="text-center mt-16 px-4">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-800 leading-tight">Welcome to <span class="text-gradient">RallySpot</span></h1>
            <p class="mt-4 text-lg sm:text-xl text-gray-600 max-w-2xl mx-auto">Your streamlined solution for effortless parking allocation. Book, manage, and secure your spot with ease.</p>
        </div>

        <!-- All Features -->
        <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 px-4">
            <!-- Feature Card: Check Availability -->
            <a href="avaiableslot.php" class="block bg-white p-6 rounded-xl container-shadow flex flex-col items-center text-center transition-transform transform hover:scale-105 duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-500 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Check Availability</h3>
                <p class="text-gray-600">Quickly find and reserve an available parking spot in your desired location.</p>
            </a>
            <!-- Feature Card: Book a Slot -->
            <a href="booking.php" class="block bg-white p-6 rounded-xl container-shadow flex flex-col items-center text-center transition-transform transform hover:scale-105 duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-500 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Book a Slot</h3>
                <p class="text-gray-600">Quickly find and reserve an available parking spot in your desired location.</p>
            </a>
            <!-- Feature Card: Cancel Booking -->
            <a href="my_bookings.php" class="block bg-white p-6 rounded-xl container-shadow flex flex-col items-center text-center transition-transform transform hover:scale-105 duration-300">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-500 mb-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4 14H6V16H4V14ZM4 8H6V10H4V8ZM4 20H6V22H4V20ZM12 8H20V10H12V8ZM12 14H20V16H12V14ZM12 20H20V22H12V20ZM8 8H10V10H8V8ZM8 14H10V16H8V14ZM8 20H10V22H8V20Z"/></svg>
    <h3 class="text-xl font-semibold text-gray-800 mb-2">My Bookings</h3>
    <p class="text-gray-600">Easily see your reservation and cancel if your plans change and free up the spot.</p>
</a>
            <!-- Feature Card: Change Password -->
            <a href= "reset_password.php" class="block bg-white p-6 rounded-xl container-shadow flex flex-col items-center text-center transition-transform transform hover:scale-105 duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-blue-500 mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14v-4h4v4h-4zm0-6V8h4v2h-4z"/></svg>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Change Password</h3>
                <p class="text-gray-600">Keep your account secure by updating your password whenever you need.</p>
            </a>
        </div>
    </div>
    <script>
        const colors = [
    '#FFD700', // Gold
    '#FFFF00', // Yellow
    '#FFC0CB', // Pink
    '#ADD8E6', // Light Blue
    '#90EE90', // Light Green
    '#FFA07A', // Light Salmon
    '#DDA0DD', // Plum
    '#AFEEEE'  // Pale Turquoise
];

const shapeContainer = document.getElementById('bubble-container'); 
let lastShapeTime = 0;
const throttleDelay = 30; // Milliseconds between star creations

function createStar(x, y) {
    const star = document.createElement('div');
    star.className = 'star'; 
    
    // Set a random color for the star
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    star.style.backgroundColor = randomColor; // Directly apply background color to the star div

    // Set a random size for each star
    const size = Math.random() * 20 + 10; // Size between 10px and 30px
    star.style.width = size + 'px';
    star.style.height = size + 'px';
    
    // Adjust the position to center the star on the cursor
    star.style.left = (x - size / 2) + 'px'; 
    star.style.top = (y - size / 2) + 'px';
    
    shapeContainer.appendChild(star);

    // Remove the star after its animation completes
    setTimeout(() => {
        star.remove();
    }, 4000); // Must match the CSS animation duration
}

document.addEventListener('mousemove', function(e) {
    const currentTime = Date.now();
    if (currentTime - lastShapeTime > throttleDelay) {
        createStar(e.clientX, e.clientY);
        lastShapeTime = currentTime;
    }
});
        </script>
</body>
</html>
