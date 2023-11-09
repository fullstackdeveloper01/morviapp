@extends('layouts.app', ['title' => __('Brands')])

@section('content')

    @include('brands.partials.header', ['title' => __('Add Brand')])

    <div class="container-fluid mt--7">

        <div class="row">

            <div class="col-xl-12 order-xl-1">

                <div class="card bg-secondary shadow">

                    <div class="card-header bg-white border-0">

                        <div class="row align-items-center">

                            <div class="col-8">

                                <!--<h3 class="mb-0">{{ __('brands Management') }}</h3>-->

                            </div>

                            <div class="col-4 text-right">

                                <a href="{{ route('brands.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>

                            </div>

                        </div>

                    </div>

                    <div class="card-body">

                        <!--<h6 class="heading-small text-muted mb-4">{{ __('brands information') }}</h6>-->

                        <div class="pl-lg-4">

                            <form method="post" action="{{ route('brands.store') }}" enctype="multipart/form-data" autocomplete="off">

                                @csrf

                                    <div class="row">

                                        <div class=" col-md-5 form-group{{ $errors->has('brand_name') ? ' has-danger' : '' }}">

                                            <label class="form-control-label" for="brand_name">{{ __('Brand') }}</label>

                                            <input type="text" name="brand_name" id="brand_name" class="form-control form-control-alternative{{ $errors->has('brand_name') ? ' is-invalid' : '' }}" maxlength="30" placeholder="{{ __('Name') }}" value="" required>

                                            @if ($errors->has('brand_name'))

                                                <span class="invalid-feedback" role="alert">

                                                    <strong>{{ $errors->first('brand_name') }}</strong>

                                                </span>

                                            @endif

                                        </div>
                        
                                        <div class="col-md-5 form-group{{ $errors->has('brand_icon') ? ' has-danger' : '' }}">

                                            <label class="form-control-label" for="brand_icon">{{ __('Icon') }}</label>
                                            <input type="file" name="brand_icon" id="brand_icon" class="form-control {{ $errors->has('brand_icon') ? ' is-invalid' : '' }}">

                                            @if ($errors->has('brand_icon'))

                                                <span class="invalid-feedback" role="alert">

                                                    <strong>{{ $errors->first('brand_icon') }}</strong>

                                                </span>

                                            @endif
											<span class="text-danger" id="iconerror" role="alert"></span>

                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="form-group col-md-4 pull-right">

                                            <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>

                                        </div>

                                    </div>

                                </form>

                            </div>

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
			
			$("#brand_icon").change(function () {

                var fileExtension = ['jpeg', 'jpg', 'png'];

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



