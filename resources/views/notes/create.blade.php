@extends('layouts.app', ['title' => __('Note')])

@section('content')

    @include('notes.partials.header', ['title' => __('Add Note')])

    <div class="container-fluid mt--7">

        <div class="row">

            <div class="col-xl-12 order-xl-1">

                <div class="card bg-secondary shadow">

                    <div class="card-header bg-white border-0">

                        <div class="row align-items-center">

                            <div class="col-8">

                                <!--<h3 class="mb-0">{{ __('Notes Management') }}</h3>-->

                            </div>

                            <div class="col-4 text-right">

                                <a href="{{ route('notes.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>

                            </div>

                        </div>

                    </div>

                    <div class="card-body">

                        <!--<h6 class="heading-small text-muted mb-4">{{ __('notes information') }}</h6>-->

                        <div class="pl-lg-4">

                            <form method="post" action="{{ route('notes.store') }}" enctype="multipart/form-data" autocomplete="off">
                                @csrf                                    
                                    <div class="row">
                                        <div class=" col-md-4 form-group{{ $errors->has('title_of_notes') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="name">{{ __('Note Title') }}</label>
                                            <input type="text" name="title_of_notes" id="title_of_notes" class="form-control form-control-alternative{{ $errors->has('title_of_notes') ? ' is-invalid' : '' }}" placeholder="{{ __('Title') }}" value="" maxlength="60" required>
                                            @if ($errors->has('title_of_notes'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('title_of_notes') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="col-md-4 form-group{{ $errors->has('selling_price') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="image">{{ __('Selling Price') }}</label>
                                            <input type="text" name="selling_price" id="selling_price" class="form-control number {{ $errors->has('selling_price') ? ' is-invalid' : '' }}" required>
                                            @if ($errors->has('selling_price'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('selling_price') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class=" col-md-4 form-group{{ $errors->has('note_type') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="name">{{ __('Note Type') }}</label>
                                            <select name="note_type" id="note_type" class="form-control form-control-alternative{{ $errors->has('note_type') ? ' is-invalid' : '' }}">
                                                <option value="English">English</option>
                                                <option value="Hindi">Hindi</option>
                                            </select>
                                            @if ($errors->has('note_type'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('note_type') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        @if($coachingId == '')
                                            <div class=" col-md-4 form-group{{ $errors->has('coaching_id') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="coaching_id">{{ __('Coaching') }}</label><br>
                                                <select class="form-control" name="coaching_id">
                                                    <option value="">Select Coaching</option>
                                                    @if($coachingList)
                                                        @foreach($coachingList as $res)
                                                            <option value="{{ $res->id }}">{{ $res->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if ($errors->has('coaching_id'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('coaching_id') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <div class=" col-md-4 form-group{{ $errors->has('coaching_id') ? ' has-danger' : '' }}" style="display:none">
                                                <label class="form-control-label" for="coaching_id">{{ __('Coaching') }}</label><br>
                                                <select class="form-control" name="coaching_id">
                                                    <option value="">Select Coaching</option>
                                                    @if($coachingList)
                                                        @foreach($coachingList as $res)
                                                            <option value="{{ $res->id }}" {{ ($coachingId == $res->id)?"selected":"" }}>{{ $res->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if ($errors->has('coaching_id'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('coaching_id') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                        <div class=" col-md-4 form-group{{ $errors->has('subject_id') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="subject_id">{{ __('Subject') }}</label><br>
                                            <select class="form-control" name="subject_id" required>
                                                <option value="">Select Subject</option>
                                                @if($subjectList)
                                                    @foreach($subjectList as $res)
                                                        <option value="{{ $res->id }}">{{ $res->subject_name }}</option>
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
                                                            <option value="{{ $res->id }}">{{ $res->name }}</option>
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
                                        <div class="col-md-4 exam_type_book form-group{{ $errors->has('exam_type_book') ? ' has-danger' : '' }}">
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <label class="form-control-label" for="heading">{{ __('Heading') }}</label>
                                            <textarea class="form-control" id="heading" name="heading"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <label class="form-control-label" for="description">{{ __('Description') }}</label>
                                            <textarea class="form-control" id="description" name="description"></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group{{ $errors->has('sample_note') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="sample_note">{{ __('Sample Note') }}</label>
                                            <input type="file" name="sample_note" id="sample_note" class="form-control {{ $errors->has('sample_note') ? ' is-invalid' : '' }}" required>
                                            @if ($errors->has('sample_note'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('sample_note') }}</strong>
                                                </span>
                                            @endif
                                               <span class="text-danger" id="sampleimgerror" role="alert"></span>
                                        </div>
                                        <div class="col-md-6 form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="image">{{ __('Cover image') }}</label>
                                            <input type="file" name="image" id="image" class="form-control {{ $errors->has('image') ? ' is-invalid' : '' }}" required>
                                            @if ($errors->has('image'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('image') }}</strong>
                                                </span>
                                            @endif
                                               <span class="text-danger" id="iconerror" role="alert"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 form-group{{ $errors->has('main_note') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="main_note">{{ __('Main Note') }}</label>
                                            <input type="file" name="main_note" id="main_note" class="form-control {{ $errors->has('main_note') ? ' is-invalid' : '' }}" required>
                                            @if ($errors->has('main_note'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('main_note') }}</strong>
                                                </span>
                                            @endif
                                               <span class="text-danger" id="mainimgerror" role="alert"></span>
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

                if(this.value.length > 5) { 
                    $(this).val($(this).attr('data-previous'));
                }else{
                    $(this).attr('data-previous',this.value);
                }
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

