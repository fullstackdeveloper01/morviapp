@extends('layouts.app', ['title' => __('Category')])



@section('content')



    @include('category.partials.header', ['title' => __('Add Category')])



    <div class="container-fluid mt--7">



        <div class="row">



            <div class="col-xl-12 order-xl-1">



                <div class="card bg-secondary shadow">



                    <div class="card-header bg-white border-0">



                        <div class="row align-items-center">



                            <div class="col-8">



                                <!--<h3 class="mb-0">{{ __('category Management') }}</h3>-->



                            </div>



                            <div class="col-4 text-right">



                                <a href="{{ route('category.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>



                            </div>



                        </div>



                    </div>



                    <div class="card-body">



                        <!--<h6 class="heading-small text-muted mb-4">{{ __('category information') }}</h6>-->



                        <div class="pl-lg-4">



                            <form method="post" action="{{ route('category.store') }}" enctype="multipart/form-data" autocomplete="off">



                                @csrf

                                    <div class="row">

                                        <div class=" col-md-5 form-group{{ $errors->has('category_name') ? ' has-danger' : '' }}">

                                            <label class="form-control-label" for="category_name">{{ __('Category Name') }}</label>

                                            <input type="text" name="category_name" id="category_name" class="form-control form-control-alternative{{ $errors->has('category_name') ? ' is-invalid' : '' }}" maxlength="30" placeholder="{{ __('Category Name') }}" value="" required>

                                            @if ($errors->has('category_name'))

                                                <span class="invalid-feedback" role="alert">

                                                    <strong>{{ $errors->first('category_name') }}</strong>

                                                </span>

                                            @endif

                                        </div>
                        
                                        <div class="col-md-5 form-group{{ $errors->has('category_icon') ? ' has-danger' : '' }}">

                                            <label class="form-control-label" for="category_icon">{{ __('Icon') }}</label>
                                            <input type="file" name="category_icon" id="category_icon" class="form-control {{ $errors->has('category_icon') ? ' is-invalid' : '' }}" required>

                                            @if ($errors->has('category_icon'))

                                                <span class="invalid-feedback" role="alert">

                                                    <strong>{{ $errors->first('category_icon') }}</strong>

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
			
			$("#category_icon").change(function () {

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



