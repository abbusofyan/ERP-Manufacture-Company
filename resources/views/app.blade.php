<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'Jobson') }}</title>

    <link rel="shortcut icon" href="/jobson-logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    @vite('resources/js/app.js')
    <script>
        ;(function () {
            function id(v) {
                return document.getElementById(v);
            }

            function loadbar() {
                var ovrl = id("loader-container"),
                    prog = id("progress-bar"),
                    stat = id("progress-percent"),
                    img = document.images,
                    c = 0,
                    tot = img.length;
                if (tot == 0) return doneLoading();

                function imgLoaded() {
                    c += 1;
                    var perc = ((100 / tot * c) << 0) + "%";
                    prog.style.width = perc;
                    stat.innerHTML = perc;
                    if (c === tot) return doneLoading();
                }

                function doneLoading() {
                    ovrl.style.opacity = 0;
                    setTimeout(function () {
                        ovrl.style.display = "none";
                    }, 1200);

                }

                for (var i = 0; i < tot; i++) {
                    var tImg = new Image();
                    tImg.onload = imgLoaded;
                    tImg.onerror = imgLoaded;
                    tImg.src = img[i].src;
                }
            }

            document.addEventListener('DOMContentLoaded', loadbar, false);
        }());
    </script>
</head>
<body>
<div id="loader-container">
    <div class="loader">
        <div class="loader__logo"><span></span><span></span><span></span></div>
        <div class="loader__progress">
            <div id="progress-bar">
                <div id="progress-percent"></div>
            </div>
        </div>
    </div>
</div>
@inertia
<div id="overlay"></div>
</body>
</html>
