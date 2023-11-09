@extends('layouts.app', ['title' => __('User Details')])

@section('content')

<div class="header bg-gradient-primary pb-7 pt-5 pt-md-8">

    <div class="container-fluid">

        <div class="header-body">

          <div class="row align-items-center py-4">

            

          </div>

        </div>

    </div>

</div>

<div class="container-fluid mt--7">

    <div class="row">

        <div class="col-xl-12 order-xl-1">

            <div class="card bg-secondary shadow">

                <div class="card-header bg-white border-0">

                    <div class="row align-items-center">

                        <div class="col-8">

                            <h3 class="mb-0">{{ ucfirst($userData->name) }}</h3>

                        </div>

						<div class="col-4 text-right">

							<a href="{{ route('user.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>

						</div>

                    </div>

                </div>

                <div class="card-body">
                    <div class="nav-wrapper">

						<ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">

							<li class="nav-item">

								<a class="nav-link mb-sm-3 mb-md-0 active" id="basic-details-tab" data-toggle="tab" href="#basic-details" role="tab" aria-controls="basic-details" aria-selected="true"><i class="ni ni-bullet-list-67 mr-2"></i>{{ __ ('User Details') }}</a>

							</li>
<!--
							<li class="nav-item">

								<a class="nav-link mb-sm-3 mb-md-0" id="exams-tab" data-toggle="tab" href="#tabs-exams" role="tab" aria-controls="tabs-exams" aria-selected="false"><i class="ni ni-bullet-list-67 mr-2"></i>{{ __ ('Exams') }}</a>

							</li>-->

						</ul>

					</div>

					<br/>

					<div class="tab-content" id="myTabContent">

						<div class="tab-pane fade show active" id="basic-details" role="tabpanel" aria-labelledby="tabs-basic-details-tab">

							@include('users.tab.basic-details')

						</div>
						{{--
						<div class="tab-pane fade" id="tabs-exams" role="tabpanel" aria-labelledby="tabs-exams-tab">

							@include('users.tab.live-enquiry')

						</div>
--}}
					</div>

                </div>

            </div>

        </div>

    </div>

</div>

<br/><br/>

</div>

@endsection

@section('js')

@endsection