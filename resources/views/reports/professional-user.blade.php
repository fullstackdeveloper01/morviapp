@extends('layouts.app', ['title' => __('Reports Management')])

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
                                <h3 class="mb-0">{{ $tableName }}</h3>
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
                                    <th scope="col">{{ __('User Type') }}</th>
                                    <th scope="col">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $wsxa = 1;
                                @endphp
                                @foreach ($tableReport as $key => $res)
                                    @if(!is_null(@$res->user->id))
                                        <tr>
                                            <td data-sort="{{ strtotime($res->created_at )}}">
                                                <a class="btn badge badge-success badge-pill" href="javascript:void()">#{{ $key+1 }}</a>
                                            </td>
                                            <td>{{ $res->user->name }}</td>
                                            <td>
                                                <a href="mailto:{{ $res->user->email }}">{{ $res->user->email }}</a>
                                            </td>
                                            <td>{{ $res->user->phone }}</td>
                                            @if($res->user->role == 1)
                                                <td>{{ __('Normal') }}</td>
                                            @else
                                                <td>{{ __('Photographer') }}</td>
                                            @endif
                                            <td>
    											<a class="btn badge badge-success badge-pill" href="{{ url('professionalList/'.$res->user_id.'/'.$res->category_table_id) }}"><i class="fa fa-eye"></i></a>
    										</td>
                                        </tr>
                                    @endif
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