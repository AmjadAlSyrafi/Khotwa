<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khotwa for Change</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-green: #0d3120;
            --warm-gold: #dda15e;
            --beige: #f4ebdf;
        }

        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background-color: var(--beige);
            color: var(--dark-green);
        }

        /* Header */
        header {
            background-color: var(--dark-green);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header img {
            height: 55px;
        }

        nav a {
            color: var(--beige);
            margin-left: 1.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: var(--warm-gold);
        }

        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            padding: 5rem 2rem;
            background: var(--beige);
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--dark-green);
        }

        .hero p {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 2rem;
        }

        .cta-button {
            background-color: var(--warm-gold);
            color: #fff;
            padding: 1rem 2.2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background-color: #c68642;
            transform: translateY(-3px);
        }

        /* Footer */
        footer {
            background-color: var(--dark-green);
            color: var(--beige);
            text-align: center;
            padding: 1rem;
            margin-top: 3rem;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            nav a {
                margin-left: 1rem;
                font-size: 0.9rem;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .hero p {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <img src="{{ asset('images/logo/logo.png') }}" alt="Khotwa Logo">
        <nav>
            <a href="#">Home</a>
            <a href="#">About Us</a>
            <a href="#">Projects</a>
            <a href="#">Contact</a>
        </nav>
    </header>

    <!-- Hero -->
    <section class="hero">
        <h1>Khotwa for Change</h1>
        <p>Towards a better community, one step at a time</p>
    </section>

    <!-- Footer -->
    <footer>
        <p>© {{ date('Y') }} Khotwa for Change - All Rights Reserved</p>
    </footer>
</body>
</html>
