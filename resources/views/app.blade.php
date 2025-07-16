<!DOCTYPE html>
<html>
<head>
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="apple-touch-icon-57x57.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="apple-touch-icon-114x114.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="apple-touch-icon-72x72.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-144x144.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="apple-touch-icon-60x60.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="apple-touch-icon-120x120.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="apple-touch-icon-76x76.png"/>
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="apple-touch-icon-152x152.png"/>
    <link rel="icon" type="image/png" href="favicon-196x196.png" sizes="196x196"/>
    <link rel="icon" type="image/png" href="favicon-96x96.png" sizes="96x96"/>
    <link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16"/>
    <link rel="icon" type="image/png" href="favicon-128.png" sizes="128x128"/>
    <meta name="application-name" content="Chocolatier"/>
    <meta name="msapplication-TileColor" content="##FFB74"/>
    <meta name="msapplication-TileImage" content="mstile-144x144.png"/>
    <meta name="msapplication-square70x70logo" content="mstile-70x70.png"/>
    <meta name="msapplication-square150x150logo" content="mstile-150x150.png"/>
    <meta name="msapplication-wide310x150logo" content="mstile-310x150.png"/>
    <meta name="msapplication-square310x310logo" content="mstile-310x310.png"/>

    <title>{{ config('choco.shortname') }} - @yield('title')</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet" type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body class="blue lighten-5">
<header>
    <nav class="top-nav blue lighten-1">
        <div class="container">
            <div class="nav-wrapper">
                <a href="#" data-activates="slide-out" class="button-collapse top-nav full hide-on-large-only">
                    <i class="mdi-navigation-menu"></i>
                </a>

                <img style="vertical-align: middle;" class="img-responsive" height="48"
                     src="/images/logo/logo_lg.png">
                <a href="{{ route('home') }}" class="brand-logo"
                   style="margin-left: 15pt;">{{config('choco.shortname') }}</a>

                <ul class="right hide-on-med-and-down">
                    <li><a class="waves-effect waves-light btn orange lighten-1" style="margin: 5pt;"
                           href="{{ route('home') }}">Home</a></li>
                    <li><a class="waves-effect waves-light btn orange lighten-1" style="margin: 5pt;"
                           href="{{ route('packages.index', ['by' => 'most', 'count' => 30, 'page' => 1]) }}">Browse</a>
                    </li>
                </ul>
            </div>
        </div>

    </nav>
    <div class="container">
        <ul id="slide-out" class="side-nav">
            <li class="bold"><a href="{{ route('home') }}">Home</a></li>
            <li class="bold"><a href="{{ route('packages.index', ['by' => 'most', 'count' => 30]) }}">Browse</a></li>
            <li style="border-bottom: 1px solid #ddd;"></li>
            @foreach(config('choco.links') as $link)
                <li class="bold">
                    <a href="{{ $link['href'] }}">{!! $link['title'] !!}</a>
                </li>
            @endforeach
        </ul>
    </div>
</header>
<main>
    @yield('content')
</main>
<footer class="page-footer blue lighten-1">
    <div class="container">
        <div class="row">
            <div class="col l6 s12">
                <h5 class="white-text">{{ config('choco.name') }}</h5>

                <p class="grey-text text-lighten-4">
                    {!! config('choco.description') !!}
                </p>
            </div>
            <div class="col l4 offset-l2 s12">
                @if(!empty(config('choco.links')) && config('choco.display_links'))
                    <h5 class="white-text">Links</h5>
                    <ul>
                        @foreach(config('choco.links') as $link)
                            <li>
                                <a class="grey-text text-lighten-3"
                                   target="_blank" href="{{ $link['href'] }}">{!! $link['title'] !!}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    <div class="footer-copyright">
        <div class="container">
            Powered by <a class="grey-text text-lighten-3" target="_blank"
                          href="https://github.com/MelonSmasher/Chocolatier">Chocolatier</a>.
        </div>
    </div>
</footer>

<!--Import jQuery before materialize.js-->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="/js/bin/materialize.js"></script>

<script>
  $(function () {
    // Initialize collapse button
    $(".button-collapse").sideNav();
    // Initialize collapsible components
    $('.collapsible').collapsible();
  });
</script>
</body>
</html>