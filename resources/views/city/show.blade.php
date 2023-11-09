@extends('layouts.app', ['title' => __('Book')])

@section('content')
<div class="header bg-gradient-primary pb-7 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            
          </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ $booksDetails->book_name }} - {{ __('Book Management') }}</h3>
                        </div>
						<div class="col-4 text-right">
							<a href="{{ route('books.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
						</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table align-items-center table-flush">
									<tbody>
										<tr>
											<th width="30%">{{ __('Image') }}</th>
											<td><img src="{{url('uploads/books/'.$booksDetails->image)}}" onerror="this.onerror=null;this.src='{{url("uploads/no-image.png")}}';" width="50" height="50"></td>
										</tr>
										<tr>
											<th width="30%">{{ __('Sample Book') }}</th>
											<td><a href="{{asset('uploads/books/'.$booksDetails->sample_book_upload)}}" target="_blank" class="text-danger"><i class="ni ni-single-copy-04 ni-2x"></i></a></td>
										</tr>
										<tr>
											<th width="30%">{{ __('Main Book') }}</th>
											<td><a href="{{asset('uploads/books/'.$booksDetails->main_book_upload)}}" target="_blank" class="text-info"><i class="ni ni-single-copy-04 ni-2x"></i></a></td>
										</tr>
										<tr>
											<th scope="col">{{ __('Name') }}</th>
											<td>{{ $booksDetails->book_name }}</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Exan Name') }}</th>
											<td>{{ $booksDetails->exam->name }}</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Selling Price') }}</th>
											<td>{{ $booksDetails->selling_price }}</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Heading') }}</th>
											<td>{{ $booksDetails->heading }}</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Description') }}</th>
											<td>{!! $booksDetails->description !!}</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Status') }}</th>
											<td>
												@if($booksDetails->status == 1)
													<span class="btn badge badge-success badge-pill">Active</span>
												@else
													<span class="btn badge badge-warning badge-pill">Inactive</span>
												@endif
											</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Crated Date') }}</th>
											<td>{{ $booksDetails->created_at }}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<br/><br/>
</div>
@endsection
@section('js')
    <script>
        $('.exam-details').submit(function() {
            $('form textarea').each(function(){
                this.value = this.value.replace(/script/g, 'tagscript');
            });
        });
    </script>
@endsection