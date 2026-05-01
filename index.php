<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PARKING ALLOCATION SYSTEM</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* New cool color for the body background */
        body {
            background-color: #f8f6f0ff; 
            color: #2c3e50;
        }

        /* Image slider container styles for full screen */
        .image-slider-container {
            width: 100vw;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: -1; /* Place behind other content */
        }
        .image-slider-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        .image-slider-container img.active {
            opacity: 1;
        }
        /* Make content visible on top of the images */
        .content-container {
            position: relative;
            z-index: 1;
            padding-top: 56px; /* Adjust for navbar height */
        }
      .text-light {
    --bs-text-opacity: 1;
    color: rgb(0 86 216) !important;
}
.book {

  color: #ffffffff;
}


body {
    background-color: #1a1a1a;
    overflow: hidden;
    /* Cursor is visible */
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
<body>
   <div id="bubble-container" ></div>
<!-- Image Slider (Full Screen) -->
<div class="image-slider-container">
    <!-- Using placeholder images from placehold.co -->
    <img src="images\hd1.jpg" class="active" alt="Parking Image 1">
    <img src="images\hd2.jpg" alt="Parking Image 2">
    <img src="images\hd3.jpg" alt="Parking Image 3">
    <img src="images\hd4.jpg" alt="Parking Image 4">
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom">
    <div class="container-fluid">
        <!-- Hamburger button (3 lines) -->
        <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Centered links -->
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mx-3">
                <a class="navbar-brand" href="#">
                    <img src="images/logo.jpg" onerror="this.src='https://placehold.co/80x30/FFFFFF/000000?text=Logo'" alt="Logo" width="80" height="30" class="d-inline-block align-text-top">
                </a>
                <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="features.html">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="pricing.php">Pricing</a></li>
                <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
<!--            <li class="nav-item"><a class="nav-link" href="about.html" style="display:relative; left:50px;">Available Slot</a></li> -->

            </ul>
        </div>
        
        <!-- Right side search -->
        <form class="d-flex" role="search" method="GET" action="search.php">
            <input class="form-control me-2" type="search" name="query" placeholder="Search Location" aria-label="Search">
            <button class="btn btn-outline-light" type="submit">Search</button>
        </form>
    </div>
</nav>

<!-- Main content container -->
<div class="container mt-5 content-container text-center">
    <h1 class="display-4 fw-bold text-light">Welcome to the RallySpot</h1>
  
</div>

<!-- Bottom Button Container -->
<div class="container-fluid position-absolute bottom-0 start-50 translate-middle-x pb-5">
    <div class="text-center">
        <!-- 'Book a Slot' button in the main content area -->
           <h4 class="book">Book your slot with ease.</h4>
        <a href="login.php" class="btn btn-primary btn-lg">Book a Slot</a>
        <a href="view_slot.html" class="btn btn-primary btn-lg">Available Slot</a>
    </div>
</div>

<!-- Sidebar (Offcanvas) -->
<div class="offcanvas offcanvas-start bg-dark text-light" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Admin Options -->
        <ul class="list-unstyled">
            <li><a href="signUp.php" class="btn btn-outline-light w-100">Sign Up</a></li><br>
            <li><a href="login.php" class="btn btn-outline-light w-100">Log In</a></li><br>
            <li><a href="Adminlogin.php" class="btn btn-outline-light w-100">Admin Only</a></li>
        </ul>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // JavaScript for the automatic image slider
    document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.image-slider-container img');
        let currentImageIndex = 0;

        function showNextImage() {
            // Hide the current active image
            images[currentImageIndex].classList.remove('active');
            
            // Move to the next image, loop back to the first if at the end
            currentImageIndex = (currentImageIndex + 1) % images.length;
            
            // Show the new active image
            images[currentImageIndex].classList.add('active');
        }

        // Set an interval to change the image every 4 seconds
        setInterval(showNextImage, 4000);
    });

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
