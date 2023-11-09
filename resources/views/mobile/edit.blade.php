@extends('layouts.app', ['title' => __('Advertisement Management')])
@section('content')
    @include('advertisement.partials.header', ['title' => __('Edit Advertisement')])
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Advertisement Management') }}</h3>
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
                                @php
                                    $imgArr = [];
                                @endphp                   
                                <div class="row">
                                    <div class=" col-md-6 form-group{{ $errors->has('title') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="name">{{ __('Title') }}</label>
                                        <input type="text" name="title" id="title" class="form-control form-control-alternative{{ $errors->has('title') ? ' is-invalid' : '' }}" placeholder="{{ __('Title') }}" value="{{ $advertisement->title }}" maxlength="40" required>
                                        @if ($errors->has('title'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group{{ $errors->has('position') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="image">{{ __('Position') }}</label>
                                        <select class="form-control" name="position" onChange="selectImageType(this.value)">
                                            <option value="">Select option</option>
                                            <option value="Main Slider" {{ ($advertisement->position == "Main Slider")?"selected":"" }}>Main Slider</option>
                                            <option value="Popup" {{ ($advertisement->position == "Popup")?"selected":"" }}>Popup</option>
                                            <option value="Footer" {{ ($advertisement->position == "Footer")?"selected":"" }}>Footer</option>
                                        </select>
                                        @if ($errors->has('position'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('position') }}</strong>
                                        </span>
                                        @endif
                                        <span class="text-danger" id="iconerror" role="alert"></span>
                                    </div>                                        
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="image">{{ __('Image') }}</label>
                                        <input type="file" name="image[]" id="image_1" class="form-control {{ $errors->has('image') ? ' is-invalid' : '' }}">
                                        @if ($errors->has('image'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('image') }}</strong>
                                            </span>
                                        @endif
                                           <span class="text-danger" id="iconerror" role="alert"></span>
                                    </div>
                                    @if($advertisement->position == "Main Slider")
                                        @php
                                            $imgArr = explode(',', $advertisement->image);
                                        @endphp
                                        @if(!empty($imgArr[0]))
                                            <div class="col-md-1 form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="image">{{ __('Image') }}</label>
                                                <img src="{{asset('uploads/advertisement').'/'.$imgArr[0]}}" onerror="this.onerror=null;this.style.display='none';"style="width: 50%;height: 65%;">
                                                <input type="hidden" name="selected_img[]" value="{{ $imgArr[0] }}">
                                            </div>
                                        @endif
                                        <div class="col-md-1" id="imagetype"><br>
                                            <span onclick="addMoreImage()" style="cursor:pointer;"><i class="ni ni-fat-add ni-2x text-info"></i></span>
                                        </div>
                                    @else
                                        @if(!empty($advertisement->image))
                                            <div class="col-md-2 form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="image">{{ __('Selected Image') }}</label>
                                                <img src="{{asset('uploads/advertisement').'/'.$advertisement->image}}" onerror="this.onerror=null;this.style.display='none';"style="width: 50%;height: 65%;">
                                            </div>
                                        @endif
                                        <div class="col-md-1" id="imagetype" style="display: none;"><br>
                                            <span onclick="addMoreImage()" style="cursor:pointer;"><i class="ni ni-fat-add ni-2x text-info"></i></span>
                                        </div>
                                    @endif
                                </div>
                                <div class="row moreimage">
                                        
                                </div>
                                <div class="row">
                                    @if(count($imgArr) > 1)
                                        @php
                                            $isn = 0;                                            
                                        @endphp
                                        @foreach($imgArr as $res)
                                            @php
                                                $isn++;                                            
                                            @endphp
                                            @if($isn > 1)
                                                @if($advertisement->position == "Main Slider")
                                                    @if(!empty($res))
                                                        <div class="col-md-4 selectedimg{{ $isn }} form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                                            <label class="form-control-label" for="image">{{ __('Image') }}</label>
                                                            <input type="file" name="image[]" id="image_{{ $isn }}" class="form-control {{ $errors->has('image') ? ' is-invalid' : '' }}">
                                                            <input type="hidden" name="selected_img[]" value="{{ $res }}">
                                                        </div>
                                                        <div class="col-md-1 selectedimg{{ $isn }} form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                                            <label class="form-control-label" for="image">{{ __('Selected') }}</label>
                                                            <img src="{{asset('uploads/advertisement').'/'.$res}}" onerror="this.onerror=null;this.style.display='none';"style="width: 50%;height: 65%;">
                                                        </div>
                                                        <div class="col-md-1 selectedimg{{ $isn }} "><br>
                                                            <span onclick="removeSelectedImg({{ $isn }})" style="cursor:pointer;"><i class="ni ni-fat-remove ni-2x text-danger"></i></span>
                                                        </div>
                                                    @endif
                                                @endif
                                            @endif
                                        @endforeach
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

                if(this.value.length > 11) { 
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
            
            $("#image").change(function () {
                var fileExtension = ['jpeg', 'jpg', 'png'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    $('#iconerror').html("Only formats are allowed : "+fileExtension.join(', '))
                    // alert("Only formats are allowed : "+fileExtension.join(', '));
                    $(this).val('');
                }else{
                    $('#iconerror').html("");
                }
            });

            $("#sample_advertisement_upload").change(function () {
                var fileExtension = ['pdf', 'PDF'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    $('#sampleimgerror').html("Only formats are allowed : "+fileExtension.join(', '))
                    // alert("Only formats are allowed : "+fileExtension.join(', '));
                    $(this).val('');
                }else{
                    $('#sampleimgerror').html("");
                }
            });

            $("#main_advertisement_upload").change(function () {
                var fileExtension = ['pdf', 'PDF'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    $('#mainimgerror').html("Only formats are allowed : "+fileExtension.join(', '))
                    // alert("Only formats are allowed : "+fileExtension.join(', '));
                    $(this).val('');
                }else{
                    $('#mainimgerror').html("");
                }
            });

            var counter = {{ count($imgArr) }};
            function selectImageType(imagetype)
            {
                if(imagetype == 'Main Slider'){
                    $('#imagetype').css('display','block');
                }
                else{
                    counter = {{ count($imgArr) }};
                    $('#imagetype').css('display','none');
                    $('.moreimage').html('');
                }
            }

            function addMoreImage()
            {
                counter++;
                var html = '';
                html += '<div class="col-md-5 form-group removeimg'+counter+'">';
                html += '<label class="form-control-label" for="image">Image</label>';
                html += '<input type="file" name="image[]" id="image_'+counter+'" class="form-control">';
                html += '</div>';
                html += '<div class="col-md-1 removeimg'+counter+'""><br>';
                html += '<span onclick="removeImage('+counter+')" style="cursor:pointer;"><i class="ni ni-fat-remove ni-2x text-danger"></i></span>';
                html += '</div>';
                $('.moreimage').append(html);
            }

            function removeImage(rimg){
                $('.removeimg'+rimg).remove();
            }
            function removeSelectedImg(rimg){
                $('.selectedimg'+rimg).remove();
            }
        </script>

        @include('layouts.footers.auth')

    </div>

@endsection

