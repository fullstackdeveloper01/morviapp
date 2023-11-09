@extends('layouts.app', ['title' => __('Advertisement')])



@section('content')



    @include('advertisement.partials.header', ['title' => __('Edit Advertisement')])



    <div class="container-fluid mt--7">



        <div class="row">



            <div class="col-xl-12 order-xl-1">



                <div class="card bg-secondary shadow">



                    <div class="card-header bg-white border-0">



                        <div class="row align-items-center">



                            <div class="col-8">



                                <!--<h3 class="mb-0">{{ __('advertisement Management') }}</h3>-->



                            </div>



                            <div class="col-4 text-right">



                                <a href="{{ route('advertisement.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>



                            </div>



                        </div>



                    </div>



                    <div class="card-body">

                        <div class="pl-lg-4">



                            <form method="post" action="{{ route('advertisement.update',$advertisement) }}" enctype="multipart/form-data" autocomplete="off">



                                @csrf



                                @method('put')



                                <div class="row">



                                    <div class=" col-md-5 form-group{{ $errors->has('advertisement_type') ? ' has-danger' : '' }}">

										<label class="form-control-label" for="advertisement_type">{{ __('Type') }}</label>
										
										<select name="advertisement_type" id="advertisement_type" class="form-control form-control-alternative{{ $errors->has('advertisement_type') ? ' is-invalid' : '' }}" required>
											<option value=""></option>
											<option value="jpeg" {{ ($advertisement->advertisement_type == "jpeg")?"selected":"" }}>jpeg</option>
											<option value="jpg" {{ ($advertisement->advertisement_type == "jpg")?"selected":"" }}>jpg</option>
											<option value="png" {{ ($advertisement->advertisement_type == "png")?"selected":"" }}>png</option>
										</select>

										@if ($errors->has('advertisement_type'))

											<span class="invalid-feedback" role="alert">

												<strong>{{ $errors->first('advertisement_type') }}</strong>

											</span>

										@endif

									</div>

                        
                                    <div class="col-md-3 form-group{{ $errors->has('advertisement') ? ' has-danger' : '' }}">

                                        <label class="form-control-label" for="advertisement">{{ __('Advertisement') }}</label>

                                        <input type="file" name="advertisement" id="advertisement" class="form-control {{ $errors->has('advertisement') ? ' is-invalid' : '' }}">

                                        @if ($errors->has('advertisement'))

                                            <span class="invalid-feedback" role="alert">

                                                <strong>{{ $errors->first('advertisement') }}</strong>

                                            </span>

                                        @endif
										<span class="text-danger" id="iconerror" role="alert"></span>

                                    </div>

                                    @if($advertisement->advertisement != '')

                                        <div class="col-md-2 form-group{{ $errors->has('advertisement') ? ' has-danger' : '' }}">

                                            <img src="{{asset('uploads/advertisement').'/'.$advertisement->advertisement}}" onerror="this.onerror=null;this.style.display='none';"style="width: 50%;height: 65%;">

                                        </div>

                                    @endif

                                </div>    

                                <div class="row">

                                    <div class="form-group col-md-4 pull-right">

                                        <button type="submit" class="btn btn-success mt-4">{{ __('Update') }}</button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $('.number').keyup(function(e) {

                if(this.value == 0) this.value =this.value.replace(/[^1-9\.]/g,'');

                else if(this.value < 0) this.value =this.value.replace(/[^0-9\.]/g,''); 

                else this.value =this.value.replace(/[^0-9\.]/g,'');               



                if(this.value.length > 3) { 

                    $(this).val($(this).attr('data-previous'));

                }else{

                    $(this).attr('data-previous',this.value);

                }



            });



            $('.hours').keyup(function(e) {

                if(this.value == 0) this.value =this.value.replace(/[^1-9\.]/g,'');

                else if(this.value < 0) this.value =this.value.replace(/[^0-9\.]/g,''); 

                else this.value =this.value.replace(/[^0-9\.]/g,'');               

            });

            

            $("#advertisement").change(function () {

                var fileExtension = ['jpeg', 'jpg', 'png', 'pdf'];

                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {

                    $('#iconerror').html("Only formats are allowed : "+fileExtension.join(', '))

                    // alert("Only formats are allowed : "+fileExtension.join(', '));

                    $(this).val('');

                }else{

                    $('#iconerror').html("");

                }

            });



        </script>



        @include('layouts.footers.auth')



    </div>



@endsection



