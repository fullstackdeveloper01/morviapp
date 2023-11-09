@extends('layouts.app', ['title' => __('User Management')])

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Users') }}</h3>
                            </div>
                            {{--<div class="col-4 text-right">
                                <a href="{{ route('user.create') }}" class="btn btn-sm btn-primary">{{ __('Add user') }}</a>
                            </div>--}}
                        </div>
                    </div>
                    
                    <div class="col-12">
                        @include('partials.flash')
                    </div>

                    <div class="table-responsive">
                        <table class="table align-items-center table-flush ndntable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('ID') }}</th>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Email') }}</th>
                                    <th scope="col">{{ __('Mobile') }}</th>
                                    <th scope="col">{{ __('Place') }}</th>
                                    <th scope="col">{{ __('User Type') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                    {{--<th scope="col">{{ __('Creation Date') }}</th>--}}
                                    <th scope="col">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $wsxa = 1;
                                @endphp
                                @foreach ($users as $key => $user)
                                    <tr>
                                        <td data-sort="{{ strtotime($user->created_at )}}">
                                            <a class="btn badge badge-success badge-pill" href="javascript:void()">#{{ $key+1 }}</a>
                                        </td>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                        </td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ @$user->city->city_name }}</td>
                                        @if($user->role == 1)
                                            <td>{{ __('Normal') }}</td>
                                        @else
                                            <td>{{ __('Photographer') }}</td>
                                        @endif
                                        <td>
                                            @if($user->active == 1)
                                                <span class="btn badge badge-success badge-pill">Active</span>
                                            @else
                                                <span class="btn badge badge-warning badge-pill">Inactive</span>
                                            @endif
                                        </td>
                                        <!--<td>{{ date('d M, Y', strtotime($user->created_at)) }}</td>-->
                                        <td>
                                            <a class="btn btn-success btn-sm" href="javascript::void()" data-toggle="modal" data-target="#mailModal" onclick="sendEmailToUser('{{ $user->email }}')" ><i class="fa fa-envelope"></i> {{__('Mail') }}</a> 
                                            <a class="btn btn-primary btn-sm" href="{{ route('user.show', $user) }}"><i class="fa fa-th-list"></i> {{__('View') }}</a>

                                            <form action="{{ route('user.destroy', $user) }}" method="post">

                                                @csrf

                                                @method('delete')

                                                @if($user->active=='1')

                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirm('{{ __("Are you sure you want to Inactive this user?") }}') ? this.parentElement.submit() : ''">

                                                    <i class="fa fa-trash"></i> {{ __('Deactivate') }}

                                                    </button>

                                                @else

                                                    <button type="button" class="btn btn-success btn-sm" onclick="confirm('{{ __("Are you sure you want to Active this user?") }}') ? this.parentElement.submit() : ''">

                                                    <i class="fa fa-time"></i> {{ __('Active') }}

                                                    </button>

                                                @endif

                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            
        @include('layouts.footers.auth')
    </div>
    
@endsection