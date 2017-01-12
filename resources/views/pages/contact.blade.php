@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Report Problem</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/contact') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('firstName') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">First Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="firstName" value="{{ Auth::user()->firstName }}">

                                @if ($errors->has('firstName'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('firstName') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('lastName') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Last Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="lastName" value="{{ Auth::user()->lastName }}">

                                @if ($errors->has('lastName'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lastName') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('issue') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Issue</label>

                            <div class="col-md-6">
	                            <select class="form-control" name="issue" value="{{ old('issue') }}">
	                            	<option selected disabled hidden style="display: none" value=""> -- select an option -- </option>
								    <option>Search: Unable to find financial inst</option>
								    <option>Account: Unable to link my financial inst</option>
								    <option>Dashboard: Linked financial inst not showing up on my dashboard</option>
						    		<option>Other: Provide details below</option>
					  			</select>

                                @if ($errors->has('issue'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('issue') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
				
                        <div class="form-group{{ $errors->has('details') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Details</label>

                            <div class="col-md-6">
                               	<textarea class="form-control" rows="10" name="details" placeholder="Please provide more details about the issue here. e.g. name of inst you were unable to search. Name of bank you were not able to login etc.">{{ old('details') }}</textarea>

                                @if ($errors->has('details'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('details') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i>Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


