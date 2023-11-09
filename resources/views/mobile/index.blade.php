@extends('layouts.app', ['title' => __('Mobile')])

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
							<div class="col-4">
								<h3 class="mb-0">{{ __('Settings Management') }}</h3>
							</div>
						</div>
					</div>
					<div class="card-body">
					    <div class="row align-items-center">
							<div class="col-3">
								<div class="box shadow border">
								    1
							    </div>
							</div>
							<div class="col-3">
								<div class="box shadow border">
								    2
							    </div>
							</div>
							<div class="col-3">
								<div class="box shadow border">
								    3
							    </div>
							</div>
							<div class="col-3">
								<div class="box shadow border">
								    4
							    </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        @include('layouts.footers.auth')
    </div>
@endsection
