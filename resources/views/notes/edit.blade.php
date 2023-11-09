@extends('layouts.app', ['title' => __('Note')])
@section('content')
    @include('notes.partials.header', ['title' => __('Edit Note')])
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Note Management') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('notes.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="pl-lg-4">
                            <form method="post" action="{{ route('notes.update',$note) }}" enctype="multipart/form-data" autocomplete="off">
                                @csrf
                                @method('put')                                
                                <div class="row">
                                    <div class=" col-md-4 form-group{{ $errors->has('title_of_notes') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="name">{{ __('Note Title') }}</label>
                                        <input type="text" name="title_of_notes" maxlength="60" id="title_of_notes" class="form-control form-control-alternative{{ $errors->has('title_of_notes') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}"  value="{{$note->title_of_notes}}" required>
                                        @if ($errors->has('title_of_notes'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('title_of_notes') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 form-group{{ $errors->has('selling_price') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="image">{{ __('Selling Price') }}</label>
                                        <input type="text" name="selling_price" id="selling_price" class="form-control number {{ $errors->has('image') ? ' is-invalid' : '' }}" value="{{$note->selling_price}}" required>
                                        @if ($errors->has('selling_price'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('selling_price') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class=" col-md-4 form-group{{ $errors->has('note_type') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="name">{{ __('Note Type') }}</label>
                                            <select name="note_type" id="note_type" class="form-control form-control-alternative{{ $errors->has('note_type') ? ' is-invalid' : '' }}">
                                                <option value="English" {{ ($note->note_type == "English")?"selected":"" }}>English</option>
                                                <option value="Hindi" {{ ($note->note_type == "Hindi")?"selected":"" }}>Hindi</option>
                                            </select>
                                            @if ($errors->has('note_type'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('note_type') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                </div>
                                <div class="row">
                                    <div class=" col-md-4 form-group{{ $errors->has('coaching_id') ? ' has-danger' : '' }}" style="display: none;">
                                        <label class="form-control-label" for="coaching_id">{{ __('Coaching') }}</label><br>
                                        <select class="form-control" name="coaching_id">
                                            <option value="">Select Coaching</option>
                                            @if($coachingList)
                                                @foreach($coachingList as $res)
                                                    <option value="{{ $res->id }}" {{ ($note->coaching_id == $res->id)?"selected":"" }}>{{ $res->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('coaching_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('coaching_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class=" col-md-4 form-group{{ $errors->has('subject_id') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="subject_id">{{ __('Subject') }}</label><br>
                                            <select class="form-control" name="subject_id" required>
                                                <option value="">Select Subject</option>
                                                @if($subjectList)
                                                    @foreach($subjectList as $res)
                                                        <option value="{{ $res->id }}" {{ ($note->subject_id == $res->id)?"selected":"" }}>{{ $res->subject_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('subject_id'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('subject_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    <div class=" col-md-4 form-group{{ $errors->has('exam_id') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="exam_id">{{ __('Exam') }}</label><br>
                                        <select class="form-control" name="exam_id" onChange="selectExamType(this.value)">
                                            <option value="">Select exam</option>
                                            @if($examList)
                                                @foreach($examList as $res)
                                                    @if(in_array($res->id, $coachingExam))
                                                        <option value="{{ $res->id }}" {{ ($note->exam_id == $res->id)?"selected":"" }}>{{ $res->name }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('exam_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('exam_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    @php
                                        $typeArray = [];
                                        $preOption = '';
                                        $mainOption = '';
                                    @endphp
                                    @if(!empty($note->exam_type))
                                    @php
                                        $typeArray = explode(', ', $note->exam_type);
                                    @endphp
                                        @if(in_array('Pre', $typeArray))
                                            @php
                                                $preOption = 'checked';
                                            @endphp
                                        @endif
                                        @if(in_array('Main', $typeArray))
                                            @php
                                                $mainOption = 'checked';
                                            @endphp
                                        @endif
                                    @endif
                                    <div class="col-md-4 exam_type_book form-group{{ $errors->has('exam_type') ? ' has-danger' : '' }}">
                                        @if(count($typeArray) > 0)
                                            <label class="form-control-label" for="exam_type">{{ __('Exam Type') }}</label><br>

                                            <div class="row">
                                                <div class="col-md-6 d-flex align-items-center">
                                                    <label class="form-control-label mr-3" for="exam_type">Pre</label>
                                                    <label class="custom-toggle">
                                                        <input type="checkbox" id="exam_type_pre" {{ $preOption }} name="exam_type_book[]" value="Pre">
                                                        <span class="custom-toggle-slider rounded-circle"></span>
                                                    </label>
                                                </div>    
                                                <div class="col-md-6 d-flex align-items-center">
                                                    <label class="form-control-label mr-3" for="exam_type">{{ __('Main') }}</label>
                                                    <label class="custom-toggle">
                                                        <input type="checkbox" id="exam_type_main" {{ $mainOption }} name="exam_type_book[]" value="Main">
                                                        <span class="custom-toggle-slider rounded-circle"></span>
                                                    </label>
                                                </div>                                                
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label class="form-control-label" for="heading">{{ __('Heading') }}</label>
                                        <textarea class="form-control" id="heading" name="heading">{{ $note->heading }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label class="form-control-label" for="description">{{ __('Description') }}</label>
                                        <textarea class="form-control" id="description" name="description">{{ $note->description }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group{{ $errors->has('sample_note') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="sample_note">{{ __('Sample Note') }}</label>
                                        <input type="file" name="sample_note" id="sample_note" class="form-control {{ $errors->has('sample_note') ? ' is-invalid' : '' }}">
                                        @if ($errors->has('sample_note'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('sample_note') }}</strong>
                                            </span>
                                        @endif
                                        <span class="text-danger" id="sampleimgerror" role="alert"></span>
                                    </div>
                                    @if(!empty($note->sample_note))
                                        <div class="col-md-2 form-group{{ $errors->has('sample_note') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="sample_note">{{ __('Sample Note') }}</label><br>
                                            <a href="{{asset('uploads/notes/'.$note->sample_note)}}" target="_blank" class="text-danger"><i class="ni ni-single-copy-04 ni-2x"></i></a>
                                        </div>
                                    @endif
                                    <div class="col-md-4 form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="image">{{ __('Cover image') }}</label>
                                        <input type="file" name="image" id="image" class="form-control {{ $errors->has('image') ? ' is-invalid' : '' }}">
                                        @if ($errors->has('image'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('image') }}</strong>
                                            </span>
                                        @endif
                                           <span class="text-danger" id="iconerror" role="alert"></span>
                                    </div>
                                    @if(!empty($note->image))
                                        <div class="col-md-2 form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="image">{{ __('Selected Image') }}</label>
                                            <img src="{{asset('uploads/notes').'/'.$note->image}}" onerror="this.onerror=null;this.style.display='none';"style="width: 50%;height: 65%;">
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-md-4 form-group{{ $errors->has('main_note') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="main_note">{{ __('Main Note') }}</label>
                                        <input type="file" name="main_note" id="main_note" class="form-control {{ $errors->has('main_note') ? ' is-invalid' : '' }}">
                                        @if ($errors->has('main_note'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('main_note') }}</strong>
                                            </span>
                                        @endif
                                        <span class="text-danger" id="mainimgerror" role="alert"></span>
                                    </div>
                                    @if(!empty($note->main_note))
                                        <div class="col-md-2 form-group{{ $errors->has('main_note') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="main_note">{{ __('Main Note') }}</label><br>
                                            <a href="{{asset('uploads/notes/'.$note->main_note)}}" target="_blank" class="text-info"><i class="ni ni-single-copy-04 ni-2x"></i></a>
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

            $("#sample_note").change(function () {
                var fileExtension = ['pdf', 'PDF'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    $('#sampleimgerror').html("Only formats are allowed : "+fileExtension.join(', '))
                    // alert("Only formats are allowed : "+fileExtension.join(', '));
                    $(this).val('');
                }else{
                    $('#sampleimgerror').html("");
                }
            });

            $("#main_note").change(function () {
                var fileExtension = ['pdf', 'PDF'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    $('#mainimgerror').html("Only formats are allowed : "+fileExtension.join(', '))
                    // alert("Only formats are allowed : "+fileExtension.join(', '));
                    $(this).val('');
                }else{
                    $('#mainimgerror').html("");
                }
            });            
        </script>

        @include('layouts.footers.auth')

    </div>

@endsection

