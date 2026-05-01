<?php
$location_name = "Midnapore";
$is_available = false;

// Check if a search query was submitted and if it matches "Midnapore"
if (isset($_GET['query'])) {
    $search_query = strtolower(trim($_GET['query']));

    if ($search_query === strtolower($location_name)) {
        $is_available = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Info</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
         body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d); /* Dynamic gradient */
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #fff;
            overflow: hidden; /* Prevent scrollbar from animation */
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glassmorphism Card Style */
        .cool-card {
            background: rgba(255, 255, 255, 0.08); /* More subtle transparency */
            backdrop-filter: blur(15px); /* Stronger blur for better glass effect */
            border: 1px solid rgba(255, 255, 255, 0.2); /* Finer, more integrated border */
            border-radius: 20px; /* Slightly more rounded */
            box-shadow: 0 10px 40px 0 rgba(0, 0, 0, 0.4); /* Deeper shadow */
            padding: 2.5rem; /* Increased padding */
            transform: perspective(1000px) rotateY(0deg) scale(1); /* Initial state for animation */
            transition: all 0.5s ease-in-out;
            animation: cardAppear 1s ease-out forwards; /* Card specific entry animation */
        }

        .cool-card:hover {
            transform: perspective(1000px) rotateY(5deg) scale(1.02); /* Subtle 3D tilt and grow on hover */
            box-shadow: 0 15px 50px 0 rgba(0, 0, 0, 0.5); /* Enhanced shadow on hover */
        }

        /* Gradient Text for "Midnapore" */
        .gradient-text {
            background: linear-gradient(45deg, #FFD700, #FFA500); /* Gold to Orange gradient */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent; /* Fallback for browsers not supporting -webkit-text-fill-color */
        }

        /* Keyframe for card entry animation */
        @keyframes cardAppear {
            from {
                opacity: 0;
                transform: perspective(1000px) translateY(50px) rotateX(10deg) scale(0.9);
            }
            to {
                opacity: 1;
                transform: perspective(1000px) translateY(0) rotateX(0deg) scale(1);
            }
        }
        .back-button {
    position: absolute;
    top: 25px;
    left: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 15px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
    transition: transform 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.back-button:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.back-button svg {
    transition: transform 0.3s ease;
}

.back-button:hover svg {
    transform: translateX(-3px);
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
       <div id="bubble-container"></div>
      <a href="#" onclick="history.back(); return false;" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        <span>Back</span>
    </a>
    <div class="cool-card p-8 rounded-xl text-center text-white max-w-sm mx-auto flex flex-col justify-center items-center">
        <?php if ($is_available): ?>
            <!-- Content for Midnapore -->
            <p class="text-2xl mb-4 leading-relaxed">
                You're in luck! We are available in <br>
                <b class="font-semibold text-yellow-300"><?php echo $location_name; ?></b>
            </p>
            <p class="text-sm text-gray-300 italic">Book your slot now!</p>
        <?php else: ?>
            <!-- Content for other cities -->
            <p class="text-2xl mb-4 leading-relaxed">
                We are available only in <br>
                <b class="font-semibold text-yellow-300"><?php echo $location_name; ?></b><br>
                We are coming soon to your city 😎
            </p>
            <p class="text-sm text-gray-300 italic">Stay tuned for updates!</p>
        <?php endif; ?>
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
// Decrease the delay to increase the number of stars and reduce latency
const throttleDelay = 10; // New value: 10 milliseconds

function createStar(x, y) {
    const star = document.createElement('div');
    star.className = 'star'; 
    
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    star.style.backgroundColor = randomColor; 

    const size = Math.random() * 20 + 10;
    star.style.width = size + 'px';
    star.style.height = size + 'px';
    
    star.style.left = (x - size / 2) + 'px'; 
    star.style.top = (y - size / 2) + 'px';
    
    shapeContainer.appendChild(star);

    setTimeout(() => {
        star.remove();
    }, 4000); 
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