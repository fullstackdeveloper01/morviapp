@extends('layouts.app', ['title' => __('City')])
@section('content')
    @include('city.partials.header', ['title' => __('Edit City')])
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('City Management') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('city.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="pl-lg-4">
                            <form method="post" action="{{ route('city.update',$city) }}" enctype="multipart/form-data" autocomplete="off">
                                @csrf
                                @method('put')                                                             
                                <div class="row">
                                    <div class=" col-md-5 form-group{{ $errors->has('state_id') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="state_id">{{ __('State') }}</label><br>
                                        <select class="form-control" name="state_id">
                                            <option value="">Select State</option>
                                            @if($stateList)
                                                @foreach($stateList as $res)
                                                    <option value="{{ $res->id }}" {{ ($res->id == $city->state_id)?"selected":"" }}>{{ $res->state_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('state_id'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('state_id') }}</strong>
                                            </span>
                                        @endif
                                    </div> 
                                    <div class=" col-md-6 form-group{{ $errors->has('city_name') ? ' has-danger' : '' }}">
                                        <label class="form-control-label" for="city_name">{{ __('City') }}</label>
                                        <input type="text" name="city_name" id="city_name" class="form-control form-control-alternative{{ $errors->has('city_name') ? ' is-invalid' : '' }}" placeholder="{{ __('State') }}" value="{{ $city->city_name }}" maxlength="40" required>
                                        @if ($errors->has('city_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('city_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>                                       
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
        </script>

        @include('layouts.footers.auth')

    </div>

@endsection

