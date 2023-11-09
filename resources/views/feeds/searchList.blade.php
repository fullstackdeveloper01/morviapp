    <table class="table align-items-center table-flush">
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
         @if(!empty($searchData) && count($searchData) > 0)
          @foreach ($searchData as $key => $feeds)
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
            @else
        <tr>
            <td>
                <p class="text-center" > No record found</p>
              </td>
        </tr>
        @endif
    </table>

