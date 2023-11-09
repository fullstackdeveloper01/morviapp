@extends('layouts.app', ['title' => __('Notes')])

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
                                <h3 class="mb-0">{{ __('Notes List') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('notes.create') }}" class="btn btn-sm btn-primary">{{ __('Add Note') }}</a>
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
                                    <th scope="col">{{ __('Image') }}</th>
                                    <th scope="col">{{ __('Note Title') }}</th>
                                    <th scope="col">{{ __('Exan Name') }}</th>
                                    <th scope="col">{{ __('Subject Name') }}</th>
                                    <th scope="col">{{ __('Heading') }}</th>
                                    <th scope="col">{{ __('Selling Price') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col" class="text-center" width="20%">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notesList as $key => $note)
                                    <tr>
                                        <td data-sort="{{ strtotime($note->created_at )}}">
                                            <a class="btn badge badge-success badge-pill" href="{{ route('notes.show', $note) }}">#{{ $key+1 }}</a>
                                        </td>
                                        <td><img src="{{url('uploads/notes/'.$note->image)}}" onerror="this.onerror=null;this.src='{{url("uploads/no-image.png")}}';" width="50" height="50"></td>
                                        <td><a class="btn badge badge-success badge-pill" href="{{ route('notes.show', $note) }}">{{ $note->title_of_notes }}</a></td>
                                        <td>{{ $note->exam->name }}</td>
                                        <td>{{ $note->subject->subject_name }}</td>
                                        <td ><a href="javascript::void(0)" data-toggle="tooltip" data-placement="top" title="{{ $note->heading }}">{{ substr($note->heading, 0, 30).' ...' }}</a></td>
                                        <td>{{ $note->selling_price }}</td>
                                        <td>@if($note->status=='1')
                                                {{ "Active" }}
                                            @else
                                                {{ "Inactive" }}
                                            @endif
                                        </td>
                                        <td class="d-flex">
                                            <a class="btn btn-info btn-sm" href="{{ route('notes.show', $note) }}"><i class="fa fa-th-list"></i> {{ __('View') }}</a>
                                            <a class="btn btn-primary btn-sm" href="{{ route('notes.edit', $note) }}"><i class="fa fa-edit"></i> {{__('Edit') }}</a>
                                            <form action="{{ route('notes.destroy', $note) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                @if($note->status=='1')
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirm('{{ __("Are you sure you want to Deactive this note?") }}') ? this.parentElement.submit() : ''">
                                                    <i class="fa fa-trash"></i> {{ __('Deactivate') }}
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-success btn-sm" onclick="confirm('{{ __("Are you sure you want to Active this note?") }}') ? this.parentElement.submit() : ''">
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
