@extends('layouts.app', ['title' => __('Feed')])
@section('content')
    @include('state.partials.header', ['title' => __('Edit Feed')])
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Feed Management') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ url('feeds') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="pl-lg-4">
                            <form method="post" action="{{route('feeds.update',$feeds->id)}}" enctype="multipart/form-data" autocomplete="off">
                                @csrf
                                @method('put')                                                             
                                <div class="row">
                                    <div class=" col-md-6 form-group{{ $errors->has('user_name') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="user_name">{{ __('User Name') }}</label>
                                        <input type="text" name="user_name" id="user_name" class="form-control form-control-alternative{{ $errors->has('user_name') ? ' is-invalid' : '' }}" placeholder="{{ __('user_name') }}" value="{{$feeds->user_name}}" maxlength="40" readonly>
                                        @if ($errors->has('user_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('user_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>       
                                     <div class=" col-md-6 form-group{{ $errors->has('user_name') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="user_name">{{ __('Text') }}</label>
                                        <input type="text" name="text" id="text" class="form-control form-control-alternative{{ $errors->has('text') ? ' is-invalid' : '' }}" placeholder="{{ __('text') }}" value="{{$feeds->text}}" maxlength="40" required>
                                        @if ($errors->has('text'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('text') }}</strong>
                                            </span>
                                        @endif
                                    </div>  
                                    @if($feeds->image)
                                    <div class=" col-md-6 form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="image">{{ __('Post Image ') }}</label>
                                        <input type="file" name="image[]" id="image" class="form-control form-control-alternative{{ $errors->has('image') ? ' is-invalid' : '' }}" placeholder="{{ __('image') }}" value="{{$feeds->image}}" maxlength="40" >
                                        @if ($errors->has('image'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('image') }}</strong>
                                            </span>
                                        @endif
                                    </div>  
                                     @endif
                                    @if($feeds->video)
                                    <div class=" col-md-6 form-group{{ $errors->has('image') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="image">{{ __('Post Video ') }}</label>
                                        <input type="file" name="video[]" id="video" class="form-control form-control-alternative{{ $errors->has('video') ? ' is-invalid' : '' }}" placeholder="{{ __('video') }}" value="{{$feeds->video}}" maxlength="40">
                                        @if ($errors->has('video'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('video') }}</strong>
                                            </span>
                                        @endif
                                    </div>  
                                    @endif
                                     <div class=" col-md-6 form-group{{ $errors->has('location') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="location">{{ __('Location') }}</label>
                                        <input type="text" name="location" id="location" class="form-control form-control-alternative{{ $errors->has('text') ? ' is-invalid' : '' }}" placeholder="{{ __('location') }}" value="{{$feeds->location_name}}" maxlength="40" readonly>
                                        @if ($errors->has('location'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('location') }}</strong>
                                            </span>
                                        @endif
                                    </div>                             
                                </div>  
                                <div class="row">
                                  @php
                                   $videos = $feeds->video;
                                    if (!empty($videos)) {
                                      $newVideos = explode(",", $videos);
                                    } else {
                                        $newVideos = [];
                                    }      
                                   $images = $feeds->image;
                                   if (!empty($images)) {
                                      $newImage = explode(",", $images);
                                    } else {
                                        $newImage = [];
                                    } 
                                  @endphp
                                @if(count($newVideos) > 0)
                                 @foreach($newVideos as $video)
                                    <div class="col-md-4">
                                        <video width="65%" height="60%" controls>
                                          <source src="{{ asset('post/'.$video) }}" type="video/mp4">
                                        </video>
                                    </div>
                                 @endforeach
                                 @else
                                    @foreach($newImage as $image)
                                    <div class="col-md-4">
                                      <img src="{{ asset('/post/'.$image) }}" style="height:120px; width:200px"/>
                                    </div>
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

        @include('layouts.footers.auth')

    </div>

@endsection

