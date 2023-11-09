@extends('layouts.app', ['title' => __('Monitor Management')])
<style>
    p {
      display: inline;
    }
</style>
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
                                <h3 class="mb-0">{{ __('Advertisement') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('monitor.index') }}" class="btn btn-sm btn-primary">{{ __('Back') }}</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush ndntable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('ID') }}</th>
                                    <th scope="col">{{ __('Type') }}</th>
                                    <th scope="col">{{ __('Advertisement') }}</th>
                                    <th scope="col">{{ __('Valid Till') }}</th>
                                    <th scope="col">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userAdvertisementList as $key => $res)
                                    <tr>
                                        <td data-sort="{{ strtotime($res->created_at )}}">
                                            <a class="btn badge badge-success badge-pill" href="javascript:void()">#{{ $key+1 }}</a>
                                        </td>
                                        <td>{{ $res->advertisement_type }}</td>
                                        @if($res->advertisement_type == 'Photo')
										    <td>
                                                <img src="/uploads/advertisement/{{ $res->advertisement }}" width="60" height="65">
                                            </td>
										@else
											<td>{{ $res->advertisement }}</td>
										@endif
                                        @if($res->valid_till == 0)
                                            <td><p id="statusid{{ $res->id }}">--</p></td>
                                        @elseif(time() < $res->valid_till )
                                            <td>
                                                <p id="days{{ $res->id }}"></p>
                                                <p id="hours{{ $res->id }}"></p>
                                                <p id="mins{{ $res->id }}"></p>
                                                <p id="secs{{ $res->id }}"></p>
                                                <p id="end{{ $res->id }}"></p>
                                                {{--<span id="statusid{{ $res->id }}">{{ date('d, M Y H:s', $res->valid_till) }}</span>--}}
                                            <script>
                                                // The data/time we want to countdown to
                                                var countDownDate{{ $res->id }} = new Date("{{ date('d, M Y H:s:i', $res->valid_till) }}").getTime();

                                                // Run myfunc every second
                                                var myfunc{{ $res->id }} = setInterval(function() {

                                                    var now{{ $res->id }} = new Date().getTime();
                                                    var timeleft{{ $res->id }} = countDownDate{{ $res->id }} - now{{ $res->id }};
                                                        
                                                    // Calculating the days, hours, minutes and seconds left
                                                    var days{{ $res->id }} = Math.floor(timeleft{{ $res->id }} / (1000 * 60 * 60 * 24));
                                                    var hours{{ $res->id }} = Math.floor((timeleft{{ $res->id }} % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                    var minutes{{ $res->id }} = Math.floor((timeleft{{ $res->id }} % (1000 * 60 * 60)) / (1000 * 60));
                                                    var seconds{{ $res->id }} = Math.floor((timeleft{{ $res->id }} % (1000 * 60)) / 1000);
                                                        
                                                    // Result is output to the specific element
                                                    document.getElementById("days{{ $res->id }}").innerHTML = days{{ $res->id }} + "d ";
                                                    document.getElementById("hours{{ $res->id }}").innerHTML = hours{{ $res->id }} + "h ";
                                                    document.getElementById("mins{{ $res->id }}").innerHTML = minutes{{ $res->id }} + "m " 
                                                    document.getElementById("secs{{ $res->id }}").innerHTML = seconds{{ $res->id }} + "s " 
                                                        
                                                    // Display the message when countdown is over
                                                    if (timeleft{{ $res->id }} < 0) {
                                                        clearInterval(myfunc{{ $res->id }});
                                                        document.getElementById("days{{ $res->id }}").innerHTML = "";
                                                        document.getElementById("hours{{ $res->id }}").innerHTML = "";
                                                        document.getElementById("mins{{ $res->id }}").innerHTML = "";
                                                        document.getElementById("secs{{ $res->id }}").innerHTML = "";
                                                        document.getElementById("end{{ $res->id }}").innerHTML = "Expired";
                                                    }
                                                }, 1000);
                                            </script>
                                            </td>
                                        @else
                                            <td><p id="statusid{{ $res->id }}">Expired</p></td>
                                        @endif
                                        <td>
                                            <a class="btn btn-success btn-sm" href="javascript::void()" data-toggle="modal" data-target="#mailModal" onclick="sendEmailToUser('{{ $res->user->email }}')" ><i class="fa fa-envelope"></i> {{__('Mail') }}</a> 
                                            @if($res->status == 0)
                                                <a id="tablerowid{{ $res->id }}" class="btn badge badge-success badge-pill" onClick="approveDisapproveAdvertisement('{{ $res->id }}')" href="javascript::void(0);">Approve</a>
                                            @else
                                                <a id="tablerowid{{ $res->id }}" class="btn badge badge-warning badge-pill" onClick="approveDisapproveAdvertisement('{{ $res->id }}')" href="javascript::void(0);">Disapprove</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            
        @include('layouts.footers.auth')
    </div>
    <script type="text/javascript">
        function approveDisapproveAdvertisement(rowid) {
            if (confirm("Are you sure you want to change this?") == true) {
                var status = 0;
                var className = $('#tablerowid'+rowid).attr('class');
                if(className == 'btn badge badge-success badge-pill'){
                    $('#tablerowid'+rowid).removeClass('btn badge badge-success badge-pill');
                    $('#tablerowid'+rowid).addClass('btn badge badge-warning badge-pill');
                    $('#tablerowid'+rowid).text('Disapprove');
                    status = 1;
                }
                else{
                    $('#tablerowid'+rowid).removeClass('btn badge badge-warning badge-pill');
                    $('#tablerowid'+rowid).addClass('btn badge badge-success badge-pill');
                    $('#tablerowid'+rowid).text('Approve');
                }
                $.ajax({

                    url: '/approveDisapproveAdvertisement/' + rowid+'/'+status,

                    type: 'get',

                    success: function(response) {
                        //$('#statusid'+rowid).text(response);
                        location.reload();
                    }

                });
            }
        }
    </script>    
@endsection