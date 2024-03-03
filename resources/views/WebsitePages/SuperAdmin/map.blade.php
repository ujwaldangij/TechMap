<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Doctor Video Player</title>
    <!-- Include SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        #video-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #000;
        }

        video,
        audio {
            display: block;
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>

    <div id="video-container">
        <video id="myVideo" controls>
            <source src="{{ asset('reports/ss.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <audio id="myAudio" autoplay loop muted>
        <source src="your-audio.mp3" type="audio/mpeg">
        Your browser does not support the audio tag.
    </audio>

    <!-- Include SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            var video = document.getElementById("myVideo");
            var audio = document.getElementById("myAudio");

            // Function to request fullscreen
            function requestFullscreen(element) {
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
            }

            // Play the video and audio in fullscreen
            video.addEventListener('loadeddata', function() {
                requestFullscreen(video);
                //video.play();
                // audio.play();
            });

            // Listen for video end
            video.addEventListener('ended', function() {
                // Use SweetAlert for confirmation with an image
                Swal.fire({
                    title: "Nutrition's Vital Role in Managing PCOS, Anemia, Weight Issues, Maternal Malnutrition, and GDM for a Healthy Pregnancy | Signutra, USA",
                    text: '...',
                    imageUrl: "{{ asset('logo/Logos-01.png') }}", // Specify the path to your image
                    imageWidth: 200, // Customize the image width
                    imageHeight: 200, // Customize the image height
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    // if (result.isConfirmed) {
                    //     // Redirect to the schedule route
                    //     {{ app\Models\WebsiteModels\SuperAdmin\State::where('id', session('sp1'))->update(['confirm' => 'Yes']) }}
                    //     window.location.href = "{{ route('schedule_id_edit', ['id' => session('sp1')]) }}";
                    // }else {
                    //     // Redirect to the thank you page
                    //     {{ app\Models\WebsiteModels\SuperAdmin\State::where('id', session('sp1'))->update(['confirm' => 'No']) }}
                    //     window.location.href = "{{ route('thankyou') }}";
                    // }
                    if (result.isConfirmed) {
                        // Send confirmation to backend via AJAX
                        console.log(1);
                        $.ajax({
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('update_confirmation') }}",
                            data: {
                                sp1: "{{ session('sp1') }}",
                                confirm: 'Yes'
                            },
                            success: function(response) {
                                // Redirect to the schedule route
                                window.location.href =
                                    "{{ route('schedule_id_edit', ['id' => session('sp1')]) }}";
                            }
                        });
                    } else {
                        // Send rejection to backend via AJAX
                        $.ajax({
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('update_confirmation') }}",
                            data: {
                                sp1: "{{ session('sp1') }}",
                                confirm: 'No'
                            },
                            success: function(response) {
                                // Redirect to the thank you page
                                window.location.href = "{{ route('thankyou') }}";
                            }
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>
