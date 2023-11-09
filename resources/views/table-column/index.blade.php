@extends('layouts.app', ['title' => __('Table Column')])

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

                                <h3 class="mb-0">{{ __('Table Column List') }}</h3>

                            </div>

                            <div class="col-4 text-right">

                                <a href="{{ route('tableColumn.create') }}" class="btn btn-sm btn-primary">{{ __('Add Table Column') }}</a>

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

                                    <th scope="col">{{ __('Table Name') }}</th>

                                    <th scope="col">{{ __('Category') }}</th>
									
                                    <th scope="col">{{ __('Sub Category') }}</th>
                                {{--
                                    <th scope="col">{{ __('Column Title') }}</th>

                                    <th scope="col">{{ __('Column Type') }}</th>
									
                                    <th scope="col">{{ __('Column Value') }}</th>
                                --}}
									<th scope="col">{{ __('Status') }}</th>

                                    <th scope="col" class="text-center" width="20%">{{ __('Action') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($tableColumnList as $key => $tableColumn)

                                    <tr>

                                        <td data-sort="{{ strtotime($tableColumn->created_at )}}">
                                            <a class="btn badge badge-success badge-pill" href="javascript::void()">#{{ $key+1 }}</a>
                                        </td>

                                        <td><a class="btn badge badge-success badge-pill" href="javascript::void()">{{ $tableColumn->table_name }}</a></td>
										<td>{{ $tableColumn->category->category_name }}</td>
										<td>{{ $tableColumn->sub_category->category_name }}</td>
                                        {{--
										<td>{{ ucfirst($tableColumn->column_type) }}</td>
										@if($tableColumn->column_value != '')
											@php
												$column_value_array = json_decode($tableColumn->column_value);
												$column_value_str = implode(',',$column_value_array);
											@endphp
										@else
											@php
												$column_value_array = [];
												$column_value_str = '';
											@endphp
										@endif
                                        <td>{{ ucfirst($column_value_str) }}</td>
                                        --}}
                                        <td>@if($tableColumn->status=='1')

                                                {{ "Active" }}

                                            @else

                                                {{ "Inactive" }}

                                            @endif

                                        </td>

                                        <td class="text-right d-flex align-items-right">

                                            <a class="btn btn-primary btn-sm" href="{{ route('tableColumn.edit', $tableColumn) }}"><i class="fa fa-edit"></i> {{__('Edit') }}</a>

                                            <form action="{{ route('tableColumn.destroy', $tableColumn) }}" method="post">

                                                @csrf

                                                @method('delete')

                                                @if($tableColumn->status=='1')

                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirm('{{ __("Are you sure you want to Deactive this table-column?") }}') ? this.parentElement.submit() : ''">

                                                    <i class="fa fa-trash"></i> {{ __('Deactivate') }}

                                                    </button>

                                                @else

                                                    <button type="button" class="btn btn-success btn-sm" onclick="confirm('{{ __("Are you sure you want to Active this table-column?") }}') ? this.parentElement.submit() : ''">

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

