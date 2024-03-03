<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Certificate as JPG Image</title>
    
    <!-- Preload Bootstrap CSS -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"></noscript>
    
    <style>
        /* Add custom styles here */
        /* Style for the preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>

<!-- Preloader -->
<div class="preloader" id="preloader">
    <!-- Customize your preloader animation or text here -->
    <img src="{{ asset('reports/Spinner-2.gif') }}" alt="Loading...">
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <img src="{{ asset('Certificate/'.$doctor_data->final_report) }}" alt="Certificate Image" class="img-fluid">
        </div>
        <div class="col-md-2 text-center">
            <a href="{{ asset('Certificate/'.$doctor_data->final_report) }}" download class="btn btn-primary">Download</a>
            <hr>
            <a href="{{ route('schedule') }}" class="btn btn-success text-center">Go to Dashboard</a>
        </div>
    </div>
</div>

<!-- Bootstrap JS (Preloaded) -->
<script>
    // Function to hide the preloader when the page is fully loaded
    window.addEventListener('load', function() {
        var preloader = document.getElementById('preloader');
        preloader.style.display = 'none';
    });
    
    // Dynamically load Bootstrap JS after the page content
    var bootstrapScript = document.createElement('script');
    bootstrapScript.src = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js";
    document.head.appendChild(bootstrapScript);
</script>
</body>
</html>
