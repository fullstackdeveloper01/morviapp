@extends('layouts.app', ['title' => __('Categories')])



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

                                <h3 class="mb-0">{{ __('Category List') }}</h3>

                            </div>

                            <div class="col-4 text-right">

                                <a href="{{ route('category.create') }}" class="btn btn-sm btn-primary">{{ __('Add Category') }}</a>

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

                                    <th scope="col">{{ __('Icon') }}</th>

                                    <th scope="col">{{ __('Category Name') }}</th>

                                    <th scope="col">{{ __('Status') }}</th>

                                    <th scope="col" class="text-center" width="20%">{{ __('Action') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($categoryList as $key => $category)

                                    <tr>

                                        <td data-sort="{{ strtotime($category->created_at )}}">
                                            <a class="btn badge badge-success badge-pill" href="{{ route('category.edit', $category) }}">#{{ $key+1 }}</a>
                                        </td>

                                        <td><img src="{{url('uploads/category/'.$category->category_icon)}}" onerror="this.onerror=null;this.src='{{url("uploads/no-image.png")}}';" width="50" height="50"></td>

                                        <td><a class="btn badge badge-success badge-pill" href="{{ route('category.edit', $category) }}">{{ $category->category_name }}</a></td>
                                        
                                        <td>@if($category->status=='1')

                                                {{ "Active" }}

                                            @else

                                                {{ "Inactive" }}

                                            @endif

                                        </td>

                                        <td class="text-right d-flex align-items-right">

                                            <a class="btn btn-primary btn-sm" href="{{ route('category.edit', $category) }}"><i class="fa fa-edit"></i> {{__('Edit') }}</a>

                                            <form action="{{ route('category.destroy', $category) }}" method="post">

                                                @csrf

                                                @method('delete')

                                                @if($category->status=='1')

                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirm('{{ __("Are you sure you want to Deactive this Category?") }}') ? this.parentElement.submit() : ''">

                                                    <i class="fa fa-trash"></i> {{ __('Deactivate') }}

                                                    </button>

                                                @else

                                                    <button type="button" class="btn btn-success btn-sm" onclick="confirm('{{ __("Are you sure you want to Active this Category?") }}') ? this.parentElement.submit() : ''">

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

