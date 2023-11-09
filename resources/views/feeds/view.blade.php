@extends('layouts.app', ['title' => __('Feed Management')])
<style>
        #show_image_popup{
      position: absolute; /*  so that not take place   */
      top: 50%;
      left: 50%;
      z-index: 1000; /*  adobe all elements   */
      transform: translate(-50%, -50%); /*  make center   */

      display: none; /*  to hide first time   */
    }
    #show_image_popup img{
      max-width: 90%;
      height: auto;
    }
    .card-container .inner-card {
        background-color: #fff;
        border: 1px solid #ccc;
        margin: 0px;
        padding: 0px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /*display: flex;
        align-items: center;*/
        border-radius: 10px;
        position:relative;
        /*flex-direction: row;*/
        
    }

    .card-container .inner-card img {
        max-width: 100%;
        height: auto;
        width:120px;
        height:120px;
        margin-right:20px;
        border-radius: 10px 0 0 10px;
    }

    .card-container .inner-card .content {
        text-align: left;
        width: 100%;
        height: 100%;
        padding: 100px 0;
    }

    .card-container .time-date .text-muted {
        color:#9e9e9e;
    }
    .card-container .mb-0{margin-bottom:0px;}
    .card-container .mt-0{margin-top:0px;}
    .card-container .fz-14{font-size:14px;}
    .card .overlay{
        display:none;
    }
    .inner-card.overlay button{
       margin-right:5px;
       cursor:pointer;
    }
    .inner-card:hover .overlay{
        position: absolute;
        right:20px;
        top:10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right:5px;
    }
    .carousel-control-prev, .carousel-control-next{
        bottom: 65px!important;
    }
</style>
@section('content')
      @php
       $videos = $postData->video;
        if (!empty($videos)) {
          $newVideos = explode(",", $videos);
        } else {
            $newVideos = [];
        }      
       $images = $postData->image;
       if (!empty($images)) {
          $newImage = explode(",", $images);
        } else {
            $newImage = [];
        } 
      @endphp
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card  p-2">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Feeds Details') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        @include('partials.flash')
                    </div>
                    <div class="row">
                        <div class="col-xl-10 offset-xl-1 col-lg-8 offset-lg-2 col-sm-12">
                            <div class="card-container">
                                <div class="card inner-card">                                                              
                                    <div class="row">
                                        <div class="col-xl-5 col-lg-5 col-sm-12">
                                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                                <div class="carousel-inner">
                                                    @if(count($newVideos) > 0)
                                                    @foreach($newVideos as $key =>  $video)
                                                    <div class="carousel-item {{$key == 0 ? 'active' : ''}}">
                                                        <video width="100%" height="350px" controls>
                                                            <source src="{{ asset('post/'.$video) }}" type="video/mp4">
                                                        </video>
                                                    </div>
                                                    @endforeach
                                                    @else
                                                    @foreach($newImage as $key => $image)
                                                    <div id="sliderID" class="carousel-item {{$key == 0 ? 'active' : ''}}">
                                                        <img class="d-block w-100 h-100" src="{{ asset('/post/'.$image) }}" onclick="showImage('{{ asset('/post/'.$image) }}')" alt="First slide">
                                                    </div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                                <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-xl-7 col-lg-7 col-sm-12">
                                            <div class="content">
                                                <h3 class="user-name mb-0 mt-0"><a href="{{ route('user.show', $postData->user_id)}}">{{ $postData->user_name }}</a></h3>
                                                <h3 class="time-date mt-0">
                                                    <span class="text-muted fz-14">{{$postData->email}}</span>
                                                    
                                                </h3>
                                                <p class="time-date mt-0">
                                                    <span class="text-muted fz-14">{{ date('d-M-y',strtotime($postData->created_at)) }}</span>
                                                    <span class="text-muted fz-14">{{ date('h:i:sa',strtotime($postData->created_at)) }}</span>
                                                </p>
                                                <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#likeModal">View Like</a>
                                                <a href="javascript:void(0);" class="btn btn-success btn-sm" data-toggle="modal" data-target="#commentModal">View Comment</a>
                                        

                                                <!--  Like  Modal-->
                                                <div class="modal fade" id="likeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Like people list</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                  <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                      
                                                            <div class="modal-body">
                                                                <table class="table">
                                                                    <tbody>
                                                                        @if (count($likes) > 0)
                                                                        @foreach ($likes as $key => $data)
                                                                        <tr>
                                                                          <td><a href="{{url('user',$data['user_id'])}}">{{$data['user_name']}}</a></td>
                                                                        </tr>
                                                                        @endforeach
                                                                        @else
                                                                        <td>No Likes Available For This Post</td>
                                                                    </tbody>
                                                                    @endif
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--  Comment  Modal-->
                                                <div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Comment people list</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered">
                                                                       <tbody>
                                                                            @if (count($comments) > 0)
                                                                            @foreach ($comments as $key => $data)
                                                                            <tr>
                                                                                <td>
                                                                                    <h5>Name:</h5>
                                                                                    <span class="text-muted">{{$data['user_name']}}</span>
                                                                                </td>
                                                                                <td>
                                                                                    <h5>Comment:</h5>
                                                                                    <span class="text-muted comments-p">{{$data['comment']}}</span>
                                                                                </td>
                                                                            </tr>
                                                                            @endforeach
                                                                            @else
                                                                                <td>No Comments Available For This Post</td>
                                                                            @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="overlay">
                                                <a type="button" href="{{route('feeds.delete',$postData->post_id)}}" class="btn btn-danger btn-sm">Delete</a>
                                            	<form action="{{url('feeds-status')}}" method="post" class="mb-0 mr-2">
	                                                @csrf
	                                                @method('post')
	                                                @if($postData->status == '1')
	                                                    <input type="hidden" name="id" value="{{$postData->post_id}}">
	                                                    <input type="hidden" name="status" value="0">
	                                                    <button type="button" class="btn btn-warning btn-sm" onclick="confirm('{{ __("Are you sure you want to Deactive this brands?") }}') ? this.parentElement.submit() : ''">
	                                                        <i class="fa fa-trash"></i> {{ __('Deactivate') }}
	                                                    </button>
	                                                @else
	                                                    <input type="hidden" name="id" value="{{$postData->post_id}}">
	                                                    <input type="hidden" name="status" value="1">
	                                                    <button type="button" class="btn btn-success btn-sm" onclick="confirm('{{ __("Are you sure you want to Active this brands?") }}') ? this.parentElement.submit() : ''">
	                                                        <i class="fa fa-time"></i> {{ __('Active') }}
	                                                    </button>
	                                                 @endif
                                            	</form>
                                                <a type="button" href="{{ url('feeds') }}" class="btn btn-sm btn-primary">Back</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
            </div>
        </div>
        <!-- // popup modal -->
        <div id="show_image_popup">
          <div class="close-btn-area">
            <button id="close-btn" class="btn btn-danger">close</button> <!--     close btn -->
          </div>
          <div id="image-show-area">
            <img id="large-image" src="" alt=""> <!--     popup imge -->
          </div>
        </div>
        @include('layouts.footers.auth')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script>
       function showImage(img){
        $("#large-image").attr("src", img);
        $("#show_image_popup").show();
       }
        $("#close-btn").click(function(){
          $("#show_image_popup").hide();
       });
        </script>
    </div>
@endsection