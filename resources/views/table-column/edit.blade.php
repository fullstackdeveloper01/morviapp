@extends('layouts.app', ['title' => __('Table Column')])

@section('content')

    @include('table-column.partials.header', ['title' => __('Edit Table Column')])

    <div class="container-fluid mt--7">

        <div class="row">

            <div class="col-xl-12 order-xl-1">

                <div class="card bg-secondary shadow">

                    <div class="card-header bg-white border-0">

                        <div class="row align-items-center">

                            <div class="col-8">

                                <!--<h3 class="mb-0">{{ __('Category Management') }}</h3>-->

                            </div>

                            <div class="col-4 text-right">

                                <a href="{{ route('tableColumn.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>

                            </div>

                        </div>

                    </div>

                    <div class="card-body">

                        <div class="pl-lg-4">

                            <form method="post" action="{{ route('tableColumn.update',$tableColumn) }}" enctype="multipart/form-data" autocomplete="off">

                                @csrf

                                @method('put')

                                <div class="row">
                                    <div class=" col-md-4 form-group{{ $errors->has('category_id') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="category_id">{{ __('Category') }}<span class="text-danger">*</span></label>
                                        <select class="form-control" name="category_id" onChange="getSubCategory(this.value)">
                                            <option value="">Select Category</option>
                                            @if($parentCategory)
                                                @foreach($parentCategory as $res)
                                                    <option value="{{ $res->id }}" {{ ($tableColumn->category_id == $res->id)?"selected":"" }}>{{ $res->category_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('category_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('category_id') }}</strong>
                                            </span>
                                        @endif
                                    </div> 
                                    <div class=" col-md-4 form-group{{ $errors->has('sub_category_id') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="sub_category_id">{{ __('Sub Category') }}<span class="text-danger">*</span></label>
                                        <select class="form-control sub_category_id" name="sub_category_id">
                                            <option value="">Select Sub Category</option>   
                                            @if($subCategory)
                                                @foreach($subCategory as $res)
                                                    <option value="{{ $res->id }}" {{ ($tableColumn->sub_category_id == $res->id)?"selected":"" }}>{{ $res->category_name }}</option>
                                                @endforeach
                                            @endif                         
                                        </select>
                                        @if ($errors->has('sub_category_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('sub_category_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class=" col-md-4 form-group{{ $errors->has('table_name') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="table_name">{{ __('Table Name') }}<span class="text-danger">*</span></label>
                                        <input type="text" name="table_name" id="table_name" class="form-control form-control-alternative{{ $errors->has('table_name') ? ' is-invalid' : '' }}" maxlength="50" placeholder="{{ __('Table Name') }}" value="{{ $tableColumn->table_name }}" required>
                                        @if ($errors->has('table_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('table_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if(!empty($tableColumnData))
                                    @foreach($tableColumnData as $key => $rrr)
                                        <div class="row rid{{$key}}">
                                            <div class=" col-md-4 form-group{{ $errors->has('column_name') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="column_name">{{ __('Column Title') }}<span class="text-danger">*</span></label>
                                                <input type="text" name="column_name[]" id="column_name{{$key}}" class="form-control form-control-alternative{{ $errors->has('column_name') ? ' is-invalid' : '' }}" maxlength="50" placeholder="{{ __('Column Title') }}" value="{{ $rrr->column_name }}" required>
                                                @if ($errors->has('column_name'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('column_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class=" col-md-4 form-group{{ $errors->has('column_type') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="column_type">{{ __('Column Type') }}</label>
                                                <select class="form-control column_type" name="column_type[]" onChange="getColumnType(this.value, {{$key}})">
                                                    <option value="">Select column type</option> 
                                                    <option value="file" {{ ($rrr->column_type == "file")?"selected":"" }}>Image</option>  
                                                    <option value="text" {{ ($rrr->column_type == "text")?"selected":"" }}>Text field</option>
                                                    <option value="number" {{ ($rrr->column_type == "number")?"selected":"" }}>Price</option>
                                                    <option value="textarea" {{ ($rrr->column_type == "textarea")?"selected":"" }}>Textarea</option>
                                                    <option value="datepicker" {{ ($rrr->column_type == "datepicker")?"selected":"" }}>Datepicker</option>
                                                    <option value="timepicker" {{ ($rrr->column_type == "timepicker")?"selected":"" }}>Timepicker</option>
                                                    <option value="dropdown" {{ ($rrr->column_type == "dropdown")?"selected":"" }}>Dropdown</option>                                   
                                                </select>
                                                @if ($errors->has('column_type'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('column_type') }}</strong>
                                                    </span>
                                                @endif
                                            </div> 
                                            @if($key == 0)
                                                <div class="col-md-1 form-group">
                                                    <label class="form-control-label" for="column_type">Action</label><span style="cursor: pointer;" onclick="addMoreOption()"><i class="ni ni-fat-add text-blue ni-2x"></i></span>
                                                </div>
                                            @else
                                                <div class="col-md-1 form-group">
                                                <label class="form-control-label" for="column_type">Action</label><span style="cursor: pointer;" onclick="removeOption({{$key}})"><i class="ni ni-fat-remove text-red ni-2x"></i></span>
                                            </div>
                                            @endif

                                            <div class="col-md-8 form-group{{ $errors->has('column_value') ? ' has-danger' : '' }}" id="textarea{{$key}}" style="display: {{ ($rrr->column_type == 'dropdown')?'block;':'none;'  }}">
                                                <label class="form-control-label" for="column_value">{{ __('Dropdown Option') }} <sub class="text-danger"> [Use comma(,) for seperate value]</sub></label>
                                                <textarea class="form-control" name="column_value[]">{{ $rrr->column_value }}</textarea>
                                                @if ($errors->has('column_value'))

                                                    <span class="invalid-feedback" role="alert">

                                                        <strong>{{ $errors->first('column_value') }}</strong>

                                                    </span>

                                                @endif

                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="row">
                                        <div class=" col-md-4 form-group{{ $errors->has('column_name') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="column_name">{{ __('Column Title') }}<span class="text-danger">*</span></label>
                                            <input type="text" name="column_name[]" id="column_name1" class="form-control form-control-alternative{{ $errors->has('column_name') ? ' is-invalid' : '' }}" maxlength="50" placeholder="{{ __('Column Title') }}" value="" required>
                                            @if ($errors->has('column_name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('column_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class=" col-md-4 form-group{{ $errors->has('column_type') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="column_type">{{ __('Column Type') }}</label>
                                            <select class="form-control column_type" name="column_type[]" onChange="getColumnType(this.value, 1)">
                                                <option value="">Select column type</option>                                   
                                                <option value="image">Image</option>                                   
                                                <option value="text">Text field</option>                                   
                                                <option value="textarea">Textarea</option>                                   
                                                <option value="datepicker">Datepicker</option>                                   
                                                <option value="timepicker">Timepicker</option>                                   
                                                <option value="dropdown">Dropdown</option>                                   
                                            </select>
                                            @if ($errors->has('column_type'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('column_type') }}</strong>
                                                </span>
                                            @endif
                                        </div> 

                                        <div class="col-md-1 form-group">
                                            <label class="form-control-label" for="column_type">Action</label><span style="cursor: pointer;" onclick="addMoreOption()"><i class="ni ni-fat-add text-blue ni-2x"></i></span>
                                        </div>

                                        <div class="col-md-8 form-group{{ $errors->has('column_value') ? ' has-danger' : '' }}" id="textarea1" style="display: none;">
                                            <label class="form-control-label" for="column_value">{{ __('Dropdown Option') }} <sub class="text-danger"> [Use comma(,) for seperate value]</sub></label>
                                            <textarea class="form-control" name="column_value[]"></textarea>
                                            @if ($errors->has('column_value'))

                                                <span class="invalid-feedback" role="alert">

                                                    <strong>{{ $errors->first('column_value') }}</strong>

                                                </span>

                                            @endif

                                        </div>
                                    </div>
                                @endif
                                <div class="more-option">
                                    
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

            function getColumnType(ctype, sn){
                if(ctype == 'dropdown'){
                    $('#textarea'+sn).css('display', 'block');
                }
                else{
                    $('#textarea'+sn).css('display', 'none');
                }
            }
                        
            var counter = 1 + '{{ count($tableColumnData) }}';
            function addMoreOption(){
                counter++;
                var s_html = '';
                s_html += '<div class="row rid'+counter+'">';
                s_html += '<div class="col-md-4 form-group">';
                s_html += '<label class="form-control-label" for="column_name">Column Title<span class="text-danger">*</span></label>';
                s_html += '<input type="text" name="column_name[]" id="column_name'+counter+'" class="form-control" maxlength="50" placeholder="Column Title" required>';
                s_html += '</div>';
                s_html += '<div class=" col-md-4 form-group">';
                s_html += '<label class="form-control-label" for="column_type">Column Type</label>';
                s_html += '<select class="form-control column_type" name="column_type[]" onChange="getColumnType(this.value, '+counter+')">';
                s_html += '<option value="">Select column type</option>';
                s_html += '<option value="file">Image</option>';
                s_html += '<option value="text">Text field</option>';
                s_html += '<option value="number">Price</option>';
                s_html += '<option value="textarea">Textarea</option>';
                s_html += '<option value="datepicker">Datepicker</option>';
                s_html += '<option value="timepicker">Timepicker</option>';
                s_html += '<option value="dropdown">Dropdown</option>';
                s_html += '</select></div>';
                s_html += '<div class="col-md-1 form-group">';
                s_html += '<label class="form-control-label" for="action">Action</label><span style="cursor: pointer;" onclick="removeOption('+counter+')"><i class="ni ni-fat-remove text-red ni-2x"></i></span>';
                s_html += '</div>';
                s_html += '<div class="col-md-8 form-group" id="textarea'+counter+'" style="display: none;">';
                s_html += '<label class="form-control-label" for="column_value">Dropdown Option <sub class="text-danger"> [Use comma(,) for seperate value]</sub></label>';
                s_html += '<textarea class="form-control" name="column_value[]"></textarea>';
                s_html += '</div></div>';

                $('.more-option').append(s_html);
            }

            function removeOption(rid){
                $('.rid'+rid).remove();
            }

        </script>



        @include('layouts.footers.auth')



    </div>



@endsection



