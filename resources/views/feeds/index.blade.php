@extends('layouts.app', ['title' => __('Feed Management')])

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Feeds') }}</h3>
                            </div>
                            <div class="col-lg-4" style="text-align:right">
                                <input type="text" id="searchFilter"  placeholder="Search by user name..">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        @include('partials.flash')
                    </div>
                    
                    <div class="table-responsive " >
                        <table class="table align-items-center table-flush searchList">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('S.No') }}</th>
                                    <th scope="col">{{ __('User Name') }}</th>
                                    <th scope="col">{{ __('Post Image / Video') }}</th>
                                    <th scope="col">{{ __('Date') }}</th>
                                    <th scope="col">{{ __('Time') }}</th>
                                    <th scope="col">{{ __('Action') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                              @foreach ($getAllFeeds as $key => $feeds)
                                    <tr>
                                    	<td>{{$key + 1}}</td>
                                        <td><a href="{{ route('user.show', $feeds->user_id)}}">{{ $feeds->user_name }}</a></td>
                                        @if($feeds->image)
                                        @php
                                            $newImg = explode(",",$feeds->image);
                                        @endphp
                                          <td><img src="{{ asset('post/'.$newImg[0]) }}" alt="job image" title="job image" height="50%" width="50%"></td>
                                          @elseif($feeds->video)
                                          @php
                                            $newVid = explode(",",$feeds->video);
                                         @endphp
                                         <td><video width="110%" height="110%" controls>
										 <source src="{{ asset('post/'.$newVid[0]) }}" type="video/mp4">
										</video></td>
                                        @endif
                                        <td>{{ date('d-M-y',strtotime($feeds->created_at)) }}</td>
                                        <td>{{ date('h:i:sa',strtotime($feeds->created_at)) }}</td>
                                         <td class="text-right d-flex align-items-right">
                                            <a class="btn btn-primary btn-sm" href="{{route('feeds.edit',$feeds->id)}}"><i class="fa fa-edit"></i> {{__('Edit') }}</a>
                                            <a class="btn btn-danger btn-sm" href="{{route('feeds.delete',$feeds->id)}}"><i class="fa fa-edit"></i> {{__('Delete') }}</a>
                                            <a class="btn btn-danger btn-sm" href="{{route('feeds.view',$feeds->id)}}"><i class="fa fa-edit"></i> {{__('View') }}</a>
                                            <form action="{{url('feeds-status')}}" method="post">
                                                @csrf
                                                @method('post')

                                                @if($feeds->status == '1')
                                                    <input type="hidden" name="id" value="{{$feeds->id}}">
                                                    <input type="hidden" name="status" value="0">
                                                    <button type="button" class="btn btn-warning btn-sm" onclick="confirm('{{ __("Are you sure you want to Deactive this brands?") }}') ? this.parentElement.submit() : ''">

                                                    <i class="fa fa-trash"></i> {{ __('Deactivate') }}

                                                    </button>

                                                @else
                                                    <input type="hidden" name="id" value="{{$feeds->id}}">
                                                    <input type="hidden" name="status" value="1">
                                                    <button type="button" class="btn btn-success btn-sm" onclick="confirm('{{ __("Are you sure you want to Active this brands?") }}') ? this.parentElement.submit() : ''">

                                                    <i class="fa fa-time"></i> {{ __('Active') }}

                                                    </button>

                                                @endif

                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                        </table>
                    </div>
                    <!-- pagination -->
                    <div class="card-footer py-4">   
                        <nav class="d-flex justify-content-end paginator" aria-label="...">
                            {{ $getAllFeeds->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
     @include('layouts.footers.auth')
    <script src="{{asset('js/custom.js')}}"></script>
</div>
@endsection