<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel</title>

    <!-- Scripts -->
    <script src="{{ secure_asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ secure_asset('css/app.css') }}" rel="stylesheet">
    <style>
        table {
            table-layout: fixed;
        }
    </style>

</head>

<body class="d-flex flex-column">
    <header>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <a class="navbar-brand" href="{{ route('index')}}">Analyzer</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('index')}}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="{{ route('domains.index')}}">Domains</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="flex-grow-1">
        @include('flash::message')
        @yield('content')
    </main>

    <footer class="border-top py-3 mt-5">
        <div class="container-lg">
            <div class="text-center">
                created by
                <a href="https://ru.hexlet.io/u/ini1990" target="_blank">Nikolai Ivanov</a>
            </div>
        </div>
    </footer>


</body>

</html>