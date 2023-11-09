@extends('layouts.app', ['title' => __('Note')])

@section('content')
<div class="header bg-gradient-warning pb-7 pt-5 pt-md-8">
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
                            <h3 class="mb-0">{{ $notesDetails->title_of_notes }} - {{ __('Note Management') }}</h3>
                        </div>
						<div class="col-4 text-right">
							<a href="{{ route('notes.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
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
											<td><img src="{{url('uploads/notes/'.$notesDetails->image)}}" onerror="this.onerror=null;this.src='{{url("uploads/no-image.png")}}';" width="50" height="50"></td>
										</tr>
										<tr>
											<th width="30%">{{ __('Sample Note') }}</th>
											<td><a href="{{asset('uploads/notes/'.$notesDetails->sample_note)}}" target="_blank" class="text-danger"><i class="ni ni-single-copy-04 ni-2x"></i></a></td>
										</tr>
										<tr>
											<th width="30%">{{ __('Main Note') }}</th>
											<td><a href="{{asset('uploads/notes/'.$notesDetails->main_note)}}" target="_blank" class="text-info"><i class="ni ni-single-copy-04 ni-2x"></i></a></td>
										</tr>										
										<tr>
											<th scope="col">{{ __('Name') }}</th>
											<td>{{ $notesDetails->title_of_notes }}</td>
										</tr>
										@if($notesDetails->coaching_id > 0)
											<tr>
												<th scope="col">{{ __('Coaching') }}</th>
												<td>{{ $notesDetails->coaching->name }}</td>
											</tr>
										@endif
										@if($notesDetails->subject_id > 0)
											<tr>
												<th scope="col">{{ __('Subject') }}</th>
												<td>{{ $notesDetails->subject->subject_name }}</td>
											</tr>
										@endif
										<tr>
											<th scope="col">{{ __('Exan Name') }}</th>
											<td>{{ $notesDetails->exam->name }}</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Selling Price') }}</th>
											<td>{{ $notesDetails->selling_price }}</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Heading') }}</th>
											<td>{{ $notesDetails->heading }}</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Description') }}</th>
											<td>{{ $notesDetails->description }}</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Status') }}</th>
											<td>
												@if($notesDetails->status == 1)
													<span class="btn badge badge-success badge-pill">Active</span>
												@else
													<span class="btn badge badge-warning badge-pill">Inactive</span>
												@endif
											</td>
										</tr>
										<tr>
											<th scope="col">{{ __('Crated Date') }}</th>
											<td>{{ $notesDetails->created_at }}</td>
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