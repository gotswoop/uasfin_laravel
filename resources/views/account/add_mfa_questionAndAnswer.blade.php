@extends('layouts.app')

@section('content')

{{-- */ 
$form = $providerAccountUpdateForm;
$provider = $form['providerDetails'];
$providerAccountUpdateFormJSON = htmlspecialchars(json_encode($providerAccountUpdateForm));
$questions = $form['loginForm']['row'];
$number_of_questions = sizeof($questions);
// dd($form);
/*--}}
<?php //dd($questions); ?>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        	<div class="alert alert-info">
               	<h4><strong>Step4: Multi-factor Authentication (MFA)</strong><br/><br/>
               	Message from 
  				@if ( isset($provider['baseUrl']) )
               		<a href="{{ $provider['baseUrl'] }}" target="_blank"><strong>{{ $provider['name'] }}</strong> </a>
               	@else
               		<strong>{{ $provider['name'] }} </strong>
               	@endif	
				: Please answer the following question(s)</h4>
            </div>
            <div class="panel panel-default">
            	<div class="panel-heading">
                   	@if ( isset($provider['logo']) )
                		<img src="{{ $provider['logo'] }}">
                	@endif
                	@if ( isset($provider['baseUrl']) )
                		<a href="{{ $provider['baseUrl'] }}" target="_blank"><h4>{{ $provider['name'] }}</h4></a>
                	@else
                		<h4>{{ $provider['name'] }}</h4>
                	@endif	
                </div>
                <div class="panel-body">
                {!! Form::open() !!}
                
                	{!! Form::hidden('providerAccountUpdateForm', $providerAccountUpdateFormJSON) !!}
					{!! Form::hidden('mfaType', "questionAndAnswer") !!}
					{!! Form::hidden('questions', $number_of_questions) !!}
					
                	@foreach($questions as $i => $question)
	                	{!! Form::hidden('q_'.$i.'_label', $question['label']) !!}
	                	{!! Form::hidden('q_'.$i.'_id', $question['field'][0]['id']) !!}
	                	<div class="form-group">
	                		{!! Form::Label('q_'.$i.'_label', $question['label']) !!}
	                	</div>
	                	<div class="form-group">
							{!! Form::text( 'q_'.$i.'_value', null, ['class' => 'form-control']) !!}	
						</div>
					@endforeach
					
					<div class="form-group">
						{!! Form::submit('Submit', ['class' => 'btn btn-warning form-control']) !!}	
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