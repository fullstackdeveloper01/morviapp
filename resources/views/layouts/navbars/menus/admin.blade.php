<ul class="navbar-nav">
    @if(config('app.ordering'))
        <li class="nav-item">
            <a class="nav-link" href="/">
                <i class="ni ni-tv-2 text-primary"></i> {{ __('Dashboard') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('user.index') }}">
                <i class="ni ni-circle-08 text-blue"></i> {{ __('Users') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('advertisement.index') }}">
                <i class="ni ni-single-copy-04 text-green"></i> {{ __('Advertisement') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('monitor.index') }}">
                <i class="ni ni-single-copy-04 text-pink"></i> {{ __('Monitor') }}
            </a>
        </li>
		<li class="nav-item">
            <a class="nav-link" href="{{ route('pages.index') }}">
                <i class="ni ni-ungroup text-orange"></i> {{ __('Pages') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('feeds')}}">
                <i class="ni ni-single-copy-04 text-green"></i> {{ __('Feeds ') }}
            </a>
        </li>
		<li class="nav-item">
            <a class="nav-link" href="{{ route('settings.index') }}">
                <i class="ni ni-settings text-info"></i> {{ __('Settings ') }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#navbar-table-name" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="navbar-table-name">
                <i class="ni ni-single-copy-04 text-blue"></i>
                <span class="nav-link-text">{{ __('Reports') }}</span>
            </a>

            <div class="collapse" id="navbar-table-name">
                <ul class="nav nav-sm flex-column">
                    @php
                        $sidebar_option = DB::table('category_tables')->get();
                    @endphp
                    @foreach($sidebar_option as $sidebar)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('reports/'.$sidebar->id) }}">
                                <span class="nav-link-text">{{ $sidebar->table_name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#navbar-Master" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="navbar-Master">
                <i class="ni ni-hat-3 text-red"></i>
                <span class="nav-link-text">{{ __('Master') }}</span>
            </a>

            <div class="collapse show" id="navbar-Master">
                <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('brands.index') }}">
                            <span class="nav-link-text">{{ __('Brands') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('category.index') }}">
                            <span class="nav-link-text">{{ __('Category') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('subCategory.index') }}">
                            <span class="nav-link-text">{{ __('Sub Category') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tableColumn.index') }}">
                            <span class="nav-link-text">{{ __('Table & Columns') }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('state.index') }}">
                            <span class="nav-link-text">{{ __('State') }}</span>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="{{ route('city.index') }}">
                            <span class="nav-link-text">{{ __('City') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        
    @endif
        {{--
        <li class="nav-item">
            <a class="nav-link" href="{{ route('settings.cloudupdate') }}">
                <i class="ni ni-cloud-download-95 text-blue"></i> {{ __('Updates') }}
            </a>
        </li>--}}
</ul>
