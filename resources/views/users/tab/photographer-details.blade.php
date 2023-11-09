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

                    <tr>

                        <th scope="col">{{ __('Business Name') }}</th>

                        <td>{{ $userData->business_name }}</td>

                    </tr>

                    <tr>

                        <th scope="col">{{ __('About Business') }}</th>

                        <td>{{ $userData->about_business }}</td>

                    </tr>

                    <tr>

                        <th scope="col">{{ __('Document Type') }}</th>

                        <td>{{ $userData->document_type }}</td>

                    </tr>

                    <tr>

                        <th scope="col">{{ __('Attached Document') }}</th>
                        <td>
                            @if($userData->document_type == 'pdf' || $userData->document_type == 'PDF')
                                <a href="{{ '/uploads/document/'.$userData->attached_document }}" target="_blank"><i class="fa fa-file-pdf-o fa-2x text-danger" aria-hidden="true"></i></a>
                            @else
                                <img src="{{ '/uploads/document/'.$userData->attached_document }}" onerror="this.onerror=null;this.src='{{url("uploads/no-image.png")}}';" width="60px">
                            @endif
                        </td>

                    </tr>

                    <tr>

                        <th scope="col">{{ __('Experience') }}</th>

                        <td>{{ $userData->experience }}</td>

                    </tr>

                    <tr>
                        <th>Professional</th>
                        <td>
                            <table width="100%">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Icon') }}</th>
                                    <th>{{ __('Title') }}</th>
                                </tr>
                                @if($professionalList)
                                    @foreach($professionalList as $key => $res)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td><img src="{{ '/uploads/category/'.$res->category_icon }}" width="65px" height="70px"></td>
                                            <td>{{ $res->category_name }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>