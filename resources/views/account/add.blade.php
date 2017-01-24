@extends('layouts.app')

@section('content')

{{-- */ $form = $providerDetails;  /*--}}
<?php //dd($form); ?>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        	<div class="alert alert-info">
               	<h4><strong>Step 3: </strong><br/><br/>Please enter the "{!! $form['loginForm']['row'][0]['label'] !!}" and "{!! $form['loginForm']['row'][1]['label'] !!}" for 
  				@if ( isset($form['baseUrl']) )
               		<a href="{{ $form['baseUrl'] }}" target="_blank"><strong>{{ $form['name'] }}</strong></a>
               	@else
               		<strong>{{ $form['name'] }}</strong> below.
               	@endif	
               	below.</h4>
            </div>
            <div class="panel panel-default">
            	<div class="panel-heading">
                   	@if ( isset($form['logo']) )
                		<img src="{{ $form['logo'] }}">
                	@endif
                	@if ( isset($form['baseUrl']) )
                		<a href="{{ $form['baseUrl'] }}" target="_blank"><h4>{{ $form['name'] }}</h4></a>
                	@else
                		<h4>{{ $form['name'] }}</h4>
                	@endif	
                </div>
                <div class="panel-body">
                	{!! Form::open() !!}
                	<div class="form-group">
                		{!! Form::Label('login', $form['loginForm']['row'][0]['label']) !!}
                	</div>
					<div class="form-group">
						{!! Form::text('login', null, ['class' => 'form-control']) !!}	
					</div>
					<div class="form-group">
						{!! Form::Label('password', $form['loginForm']['row'][1]['label'] ) !!}
					</div>
					<div class="form-group">
						{!! Form::password('password', null, ['class' => 'form-control']) !!}	
					</div>
					<div class="form-group">
						{!! Form::submit('Login', ['class' => 'btn btn-warning form-control']) !!}	
					</div>
	                {!! Form::close() !!}
	                @if ($errors->any())
	                	<div class="alert alert-danger">
	                		{{ $errors->first() }}
	                	</div>
	                @endif
	                <div>
	                @if( isset($form['forgetPasswordUrl']) )
	                	<p>
	                		<a href="{{ $form['forgetPasswordUrl'] }}" target="_blank">Forgot Password?</a>
	                	</p>
	                @endif
	                @if( isset($form['help']) )
	                	<p>
	                		<strong>Help from {{ $form['name'] }}:  </strong><br/><?php echo htmlspecialchars_decode(stripslashes($form['help'])); ?>
	                	</p>
	                @endif
	                </div> 
                </div>
            </div>
        </div>
    </div>
    <div align="center">Problems adding an institution? Please contact the helpdesk at 1-855-872-8673 (9am - 5pm PST) or by email: <a href="mailto:uashelp@usc.edu?subject=[via%20uasfin.usc.edu%20-%20Yodlee]">uashelp@usc.edu</a>.</div>
</div>
@endsection