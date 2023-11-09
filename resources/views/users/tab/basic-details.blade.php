<div class="row">

    <div class="col-md-12">

        <div class="table-responsive">

            <table class="table align-items-center table-flush">

                <tbody>

                    <tr>

                        <th width="30%">{{ __('ID') }}</th>

                        <td>{{ $userData->id }}</td>

					</tr>

                    <tr>

                        <th>{{ __('Image') }}</th>

                        <td>
                            <img src="{{ '/uploads/profile/'.$userData->image }}" onerror="this.onerror=null;this.src='{{url("uploads/no-image.png")}}';" width="60px">
                        </td>

                    </tr>

                    <tr>

                        <th scope="col">{{ __('Name') }}</th>

                        <td>{{ $userData->name }}</td>

					</tr>

                    <tr>

                        <th scope="col">{{ __('Contact Number') }}</th>

                        <td>{{ $userData->phone }}</td>

					</tr>

                    <tr>

                        <th scope="col">{{ __('Email') }}</th>

                        <td>{{ $userData->email }}</td>

					</tr>

                    <tr>

                        <th scope="col">{{ __('Document Type') }}</th>

                        <td>{!! $userData->document_type !!}</td>

					</tr>

                    <tr>
                        <th scope="col">{{ __('Document') }}</th>
                        <td><img src="{{url('uploads/document/'.$userData->attached_document)}}" onerror="this.onerror=null;this.src='{{url("uploads/no-image.png")}}';" width="50" height="50"></td>
                    </tr>

                    <tr>

                        <th scope="col">{{ __('Status') }}</th>

						<td>

							@if($userData->active == 1)

								<span class="btn badge badge-success badge-pill">Active</span>

							@else

								<span class="btn badge badge-warning badge-pill">Inactive</span>

							@endif

                        </td>

					</tr>

                    <tr>

                        <th scope="col">{{ __('Crated Date') }}</th>

                        <td>{{ $userData->created_at }}</td>

					</tr>

                </tbody>

            </table>

        </div>

    </div>

</div>