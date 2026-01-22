<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevRum | Developer Forum</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .brand {
            color: #00e5ff;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .btn-dev {
            background-color: #00e5ff;
            color: #000;
            font-weight: 600;
            border: none;
        }

        .btn-dev:hover {
            background-color: #00bcd4;
            color: #000;
        }
    </style>
</head>
<body>

    <!-- Landing Section -->
    <section class="hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <div class="hero-card">
                        <h1 class="display-5 fw-bold mb-3">
                            Welcome to <span class="brand">DevRum</span>
                        </h1>
                        <p class="lead mb-4">
                            A chill place for developers to ask questions, share ideas,  
                            and build cool stuff together.
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="auth/register.php" class="btn btn-dev btn-lg px-4">Join the Forum</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
