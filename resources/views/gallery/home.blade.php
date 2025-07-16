@extends('app')

@section('title')
    Home
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col m12">
                <h1>What is {{Config::get('choco.shortname')}}?</h1>
                <p>
                    {{Config::get('choco.shortname')}} is a NuGet package manager repository server.
                    To browse this repository, add <code>{{ route('api.index') }}</code> to your NuGet
                    Package Manager configuration:

                    <pre>
                        <code class="language-powershell">
                            @if (Config::get('app.site_user') != null && Config::get('app.site_password') != null)
                                choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}" -u "{{ Config::get('app.site_user') }}" -p "{{ Config::get('app.site_password') }}"
                            @elseif (Config::get('app.site_user') != null)
                                choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}" -u "{{ Config::get('app.site_user') }}" -p ""
                            @elseif (Config::get('app.site_password') != null)
                                choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}" -u "" -p "{{ Config::get('app.site_password') }}"
                            @else
                                choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}"
                            @endif
                        </code>
                    </pre>

                </p>
            </div>
        </div>

        <div class="row">
            <div class="col m4">
                <div class="card orange lighten-2">
                    <div class="card-content">
                        <span class="card-title">Unique Packages</span>
                        <p><strong>{{ $uniquePackages }}</strong> unique packages.</p>
                    </div>
                </div>
            </div>
            <div class="col m4">
                <div class="card orange lighten-2">
                    <div class="card-content">
                        <span class="card-title">Total Downloads</span>
                        <p><strong>{{ $totalDownloads }}</strong> total package downloads.</p>
                    </div>
                </div>
            </div>
            <div class="col m4">
                <div class="card orange lighten-2">
                    <div class="card-content">
                        <span class="card-title">Total Packages</span>
                        <p><strong>{{ $totalPackages }}</strong> total packages.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col m12">
                <a class="waves-effect waves-light btn orange lighten-1" href="{{ route('packages.index', ['by' => 'most', 'count' => 30, 'page' => 1]) }}">
                    <i class="material-icons left">library_books</i>
                    Browse Packages
                </a>
            </div>
        </div>
    </div>
@endsection
