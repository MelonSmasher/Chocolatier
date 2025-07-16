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
                </p>

                <h4>Chocolatey Setup</h4>

                <ul class="collapsible">
                    <li>
                        <div class="collapsible-header"><i class="material-icons">keyboard_arrow_down</i>View Setup
                            Instructions
                        </div>
                        <div class="collapsible-body">
                            <p>Optionally delete the existing default source:</p>
                            <pre><code class="language-powershell">choco source remove -n chocolatey</code></pre>

                            <p>Add the new source:</p>
                            @if (Config::get('app.site_user') != '' && Config::get('app.site_password') != '')
                                <pre><code class="language-powershell">choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}" -u "{{ Config::get('app.site_user') }}" -p "{{ Config::get('app.site_password') }}"</code></pre>
                            @elseif (Config::get('app.site_user') != '')
                                <pre><code class="language-powershell">choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}" -u "{{ Config::get('app.site_user') }}" -p ""</code></pre>
                            @elseif (Config::get('app.site_password') != '')
                                <pre><code class="language-powershell">choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}" -u "" -p "{{ Config::get('app.site_password') }}"</code></pre>
                            @else
                                <pre><code class="language-powershell">choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}"</code></pre>
                            @endif
                        </div>
                    </li>
                </ul>

                <h4>Chocolatey Installation</h4>

                <ul class="collapsible">
                    <li>
                        <div class="collapsible-header"><i class="material-icons">keyboard_arrow_down</i>View
                            Installation Instructions
                        </div>
                        <div class="collapsible-body">
                            <p>To install chocolatey from scratch:</p>
                            @if (Config::get('app.site_user') != '' && Config::get('app.site_password') != '')
                                <pre><code class="language-powershell">Set-ExecutionPolicy Bypass -Scope Process -Force; $webClient = New-Object System.Net.WebClient; $webClient.Headers.Add("Authorization", "Basic " + [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes("{{ Config::get('app.site_user') }}:{{ Config::get('app.site_password') }}"))); iex ($webClient.DownloadString('{{route('api.index')}}/ChocolateyInstall.ps1'))</code></pre>
                            @elseif (Config::get('app.site_user') != '')
                                <pre><code class="language-powershell">Set-ExecutionPolicy Bypass -Scope Process -Force; $webClient = New-Object System.Net.WebClient; $webClient.Headers.Add("Authorization", "Basic " + [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes("{{ Config::get('app.site_user') }}:"))); iex ($webClient.DownloadString('{{route('api.index')}}/ChocolateyInstall.ps1'))</code></pre>
                            @elseif (Config::get('app.site_password') != '')
                                <pre><code class="language-powershell">Set-ExecutionPolicy Bypass -Scope Process -Force; $webClient = New-Object System.Net.WebClient; $webClient.Headers.Add("Authorization", "Basic " + [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes(":{{ Config::get('app.site_password') }}"))); iex ($webClient.DownloadString('{{route('api.index')}}/ChocolateyInstall.ps1'))</code></pre>
                            @else
                                <pre><code class="language-powershell">Set-ExecutionPolicy Bypass -Scope Process -Force; iex ((New-Object System.Net.WebClient).DownloadString('{{route('api.index')}}/ChocolateyInstall.ps1'))</code></pre>
                            @endif
                        </div>

                        Delete the existing default source:
                        <pre><code class="language-powershell">choco source remove -n chocolatey</code></pre>

                        Add the new source:
                        @if (Config::get('app.site_user') != '' && Config::get('app.site_password') != '')
                            <pre><code class="language-powershell">choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}" -u "{{ Config::get('app.site_user') }}" -p "{{ Config::get('app.site_password') }}"</code></pre>
                        @elseif (Config::get('app.site_user') != '')
                            <pre><code class="language-powershell">choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}" -u "{{ Config::get('app.site_user') }}" -p ""</code></pre>
                        @elseif (Config::get('app.site_password') != '')
                            <pre><code class="language-powershell">choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}" -u "" -p "{{ Config::get('app.site_password') }}"</code></pre>
                        @else
                            <pre><code class="language-powershell">choco source add -n "{{strtolower(trim(Config::get('choco.shortname')))}}" -s "{{ route('api.index') }}"</code></pre>
                        @endif
                    </li>
                </ul>

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
                <a class="waves-effect waves-light btn orange lighten-1"
                   href="{{ route('packages.index', ['by' => 'most', 'count' => 30, 'page' => 1]) }}">
                    <i class="material-icons left">library_books</i>
                    Browse Packages
                </a>
            </div>
        </div>
    </div>
@endsection
