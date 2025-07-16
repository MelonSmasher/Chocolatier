@extends('app')

@section('title')
    {{ $package->title }}
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col m8">
                <h3>{{ $package->title }} {{ $package->version }}</h3>
                <p>
                    {{ empty($package->description) ? $package->summary : $package->description }}
                </p>
            </div>
            <div class="col m4">
                <div class="img-wrapper" style="max-width: 150px;">
                    <img class="responsive-img" src="{{ $package->getIconUrl() }}" alt="{{ $package->package_id }}"
                         onerror="this.src='{{asset('images/packageDefaultIcon.png')}}';"/>
                </div>
                <strong>{{ $package->download_count }} downloads</strong><br/>
                <strong>{{ $package->version_download_count }} downloads of {{ $package->version }}</strong><br/>
                <strong>Last updated {{ $package->created_at->toFormattedDateString() }}</strong><br/>
                <br/>
                @if(!empty($package->project_url))
                    <a href="{{$package->project_url}}">Project Site</a><br/>
                @endif
                @if(!empty($package->license_url))
                    <a href="{{$package->license_url}}">License</a><br/>
                @endif
            </div>

            @if(!empty($package->release_notes))
                <h5>Release Notes</h5>
                <p>
                <pre>{{ $package->release_notes }}</pre>
                </p>
            @endif

            <div class="col m4">
                <ul class="collapsible">
                    <li>
                        <div class="collapsible-header">
                            <h5><i class="material-icons">keyboard_arrow_down</i> Install {{ $package->title }}</h5>
                        </div>
                        <div class="collapsible-body">
                            Install the latest version of {{ $package->title }}:
                            <pre><code class="language-powershell">choco install {{ $package->package_id }}</code></pre>

                            Install the latest version {{$package->version}} of {{ $package->title }}:
                            <pre><code class="language-powershell">choco install {{ $package->package_id }} --version {{ $package->version }}</code></pre>
                        </div>
                    </li>
                </ul>
            </div>

            @if(!empty($package->getOwners()))
                <h5>Owners</h5>
                <ul>
                    @foreach($package->getOwners() as $owner)
                        <li>{{ $owner }}</li>
                    @endforeach
                </ul>
            @endif
            @if(!empty($package->getAuthors()))
                <h5>Authors</h5>
                <ul>
                    @foreach($package->getAuthors() as $author)
                        <li>{{ $author }}</li>
                    @endforeach
                </ul>
            @endif
            @if(!empty($package->copyright))
                <h5>Copyright</h5>
                <p>{{ $package->copyright }}</p>
            @endif

            @if(!empty($package->tags))
                <h5>Tags</h5>
                <p>{{ $package->tags }}</p>
            @endif

            @if(!empty($package->dependencies))
                <h5>Dependencies</h5>
                <ul>
                    @foreach(explode('|', $package->dependencies) as $dependency)
                            <?php $split = explode(':', $dependency); ?>
                        <li><a href="{{route('packages.show', $split[0]) }}">{{ $split[0] }}</a> (&ge; {{ $split[1] }})
                        </li>
                    @endforeach
                </ul>
            @endif

            <h5>Version History</h5>
            <table class="striped">
                <thead>
                <tr>
                    <th>
                        Version
                    </th>
                    <th>
                        Downloads
                    </th>
                    <th>
                        Last Updated
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($versions as  $version)
                    <tr class="{{ $version->version == $package->version ? 'bold' : '' }}">
                        <td>
                            <a href="{{route('packages.version.show', ['name' => $package->package_id, 'version' => $version->version])}}">{{ $version->version }}
                                @if($version->version == $package->version)
                                    (this version)
                                @endif
                                @if($version->is_latest_version)
                                    (latest stable)
                                @endif
                            </a>
                        </td>
                        <td>
                            {{ $version->version_download_count }}
                        </td>
                        <td>
                            {{ $version->created_at->toFormattedDateString() }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection