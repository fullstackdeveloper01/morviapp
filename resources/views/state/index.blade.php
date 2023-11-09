@extends('layouts.app', ['title' => __('State')])

@section('content')
@if(auth()->user()->role == 3)
    <div class="header bg-gradient-warning pb-8 pt-5 pt-md-8">
    </div>
@else
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>
@endif

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('State List') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('state.create') }}" class="btn btn-sm btn-primary">{{ __('Add State') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush ndntable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('S.No') }}</th>
                                    <th scope="col">{{ __('State') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col" class="text-center" width="20%">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stateList as $key => $state)
                                    <tr>
                                        <td data-sort="{{ strtotime($state->created_at )}}">
                                            <a class="btn badge badge-success badge-pill" href="javascript::void()">#{{ $key+1 }}</a>
                                        </td>
                                        
                                        <td>{{ $state->state_name }}</td>
                                        <td>@if($state->status=='1')
                                                {{ "Active" }}
                                            @else
                                                {{ "Inactive" }}
                                            @endif
                                        </td>
                                        <td class="text-right d-flex align-items-right">
                                            {{--<a class="btn btn-info btn-sm" href="{{ route('state.show', $state) }}"><i class="fa fa-th-list"></i> {{ __('View') }}</a>--}}
                                            <a class="btn btn-primary btn-sm" href="{{ route('state.edit', $state) }}"><i class="fa fa-edit"></i> {{__('Edit') }}</a>
                                            <form action="{{ route('state.destroy', $state) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                @if($state->status=='1')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirm('{{ __("Are you sure you want to Deactive this state?") }}') ? this.parentElement.submit() : ''">
                                                    <i class="fa fa-trash"></i> {{ __('Deactivate') }}
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-success btn-sm" onclick="confirm('{{ __("Are you sure you want to Active this state?") }}') ? this.parentElement.submit() : ''">
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
