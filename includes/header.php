<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Header</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #F0F4F8;
        }
        header {
            background-color: #eeff00;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            transition: transform 0.3s ease-in-out;
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        #site-name {
            color: white;
            font-size: 22px;
            display: none;
            font-weight: bold;
        }
        nav {
            display: flex;
            align-items: center;
        }
        .nav-links {
            display: flex;
            gap: 20px;
        }
        .nav-links a {
            color: #FFFFFF;
            text-decoration: none;
            padding: 10px 16px;
            font-size: 18px;
            border-radius: 8px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .nav-links a:hover {
            background-color: #FFB74D;
            color: rgb(255, 238, 0);
        }
        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 24px; /* Smaller icon */
            color: black;
            background: none;
            border: none;
            padding: 5px 10px;
            width: auto;
            margin-left: auto; /* Pushes it to the right */
        }


        #site-name {
            color: black; /* Makes the text black */
        }

        .menu-toggle {
            color: black; /* Makes the hamburger menu icon black */
        }
        #site-name {
            color: black; /* Makes the text black */
        }

        .menu-toggle {
            color: black; /* Makes the hamburger menu icon black */
        }

        @media screen and (max-width: 768px) {
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 80px;
                right: 0;
                background-color: #FFFFFF;
                width: 200px;
                border-radius: 10px;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                border: 5px solid rgb(0, 0, 0);
                padding: 20px;
            }
            .nav-links a {
                display: block;
                padding: 14px 20px;
                color: #3E8E41;
                background-color: #FFEB3B;
                border: 5px solid rgb(0, 0, 0);
            }
            .menu-toggle {
                display: block;
            }
            .nav-links.active {
                display: flex;
            }

        }
    </style>
</head>
<body>
    <header id="header">
        <div class="logo-container">
            <img src="images/logo.png" alt="click.ba Logo" class="profile-pic-header">
            <span id="site-name">click.ba</span>
        </div>
        <button class="menu-toggle" onclick="toggleMenu()">&#9776;</button>
        <nav>
            <div class="nav-links" id="nav-links">
                <a href="index.php">Home</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="welcome.php">Profile</a>
                    <a href="post.php">Create Post</a>
                    <a href="logout.php">Logout</a>
                    <a href="complaints.php">Suggestions and complaints</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <script>
        function toggleMenu() {
            document.getElementById('nav-links').classList.toggle('active');
        }

        let lastScrollY = window.scrollY;
        const header = document.getElementById("header");

        window.addEventListener("scroll", () => {
            if (window.scrollY > lastScrollY) {
                header.style.transform = "translateY(-100%)";
            } else {
                header.style.transform = "translateY(0)";
            }
            lastScrollY = window.scrollY;
        });

        function checkHeaderSpace() {
            const headerWidth = header.offsetWidth;
            const navWidth = document.getElementById("nav-links").offsetWidth;
            const siteName = document.getElementById("site-name");

            if (headerWidth - navWidth > 250) { // Adjust this threshold if needed
                siteName.style.display = "inline";
            } else {
                siteName.style.display = "none";
            }
        }

        window.addEventListener("resize", checkHeaderSpace);
        window.addEventListener("load", checkHeaderSpace);
    </script>
</body>
</html>
