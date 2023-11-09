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
                                <h3 class="mb-0">{{ count($professionalTable).' '.$tableName }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ url('reports/'.$professionalData[0]->category_table_id) }}" class="btn btn-sm btn-primary">{{ __('Back') }}</a>
                            </div>
                        </div>
                    </div>                    

                    <div class="table-responsive">
                    	@if($professionalTable)
                    		@foreach($professionalTable as $tab)
		                        <table class="table align-items-center table-flush">
		                            <thead class="thead-light">
										<tr>
											{{--<th scope="col">{{ __('ID') }}</th>--}}
											@if($tableColumnData)
												@foreach($tableColumnData as $col)
													<th scope="col">{{ $col->column_name }}</th>
												@endforeach
											@endif
											<th scope="col">{{ __('Action') }}</th>
										</tr>
		                            </thead>
		                            <tbody>
										@if($professionalData)
											@foreach($professionalData as $key => $res)
												@if($tab->table_number == $res->table_number)	
													<tr>
														{{--<td data-sort="{{ strtotime($res->created_at )}}">
															<a class="btn badge badge-success badge-pill" href="javascript:void()">#{{ $key+1 }}</a>
														</td>--}}
														@foreach($professionalResult as $rrr)
															@if($rrr->row_number == $res->row_number)		
																@if($tab->table_number == $rrr->table_number)	
																	@if($rrr->column_type == 'file')								
																		<td><img src="{{ '/'.$rrr->field_value }}" width="50" height="50"></td>
																	@else
																		<td>{{ $rrr->field_value }}</td>
																	@endif
																@endif
															@endif
														@endforeach
														<td>
															@if($res->status == 1)
																<a id="tablerowid{{ $res->row_number }}" class="btn badge badge-success badge-pill" onClick="statusTableRow('{{ $res->row_number }}')" href="javascript::void(0);">Deactivate</a>
															@else
																<a id="tablerowid{{ $res->row_number }}" class="btn badge badge-warning badge-pill" onClick="statusTableRow('{{ $res->row_number }}')" href="javascript::void(0);">Activate</a>
															@endif
														</td>
													</tr>
												@endif									
											@endforeach
										@endif
		                            </tbody>
		                        </table>
		                        <hr>
                    		@endforeach
                    	@endif
                    </div>
                </div>
            </div>
        </div>            
        @include('layouts.footers.auth')
    </div>
    <script type="text/javascript">
    	function statusTableRow(rowid) {
			if (confirm("Are you sure you want to change this?") == true) {
				var status = 0;
				var className = $('#tablerowid'+rowid).attr('class');
				if(className == 'btn badge badge-success badge-pill'){
					$('#tablerowid'+rowid).removeClass('btn badge badge-success badge-pill');
					$('#tablerowid'+rowid).addClass('btn badge badge-warning badge-pill');
					$('#tablerowid'+rowid).text('Activate');
				}
				else{
					$('#tablerowid'+rowid).removeClass('btn badge badge-warning badge-pill');
					$('#tablerowid'+rowid).addClass('btn badge badge-success badge-pill');
					$('#tablerowid'+rowid).text('Deactivate');
					status = 1;
				}
				$.ajax({

                    url: '/changeTableRowStatus/' + rowid+'/'+status,

                    type: 'get',

                    success: function(response) {
                        
                    }

                });
			}
		}
    </script>
@endsection