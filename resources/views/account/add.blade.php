@extends('layouts.app')

@section('content')

{{-- */ $form = $providerDetails[0];  /*--}}
<?php //dd($form); ?>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{ $form['name'] }}</div>		
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
						{!! Form::submit('Login', ['class' => 'btn btn-primary form-control']) !!}	
					</div>
	                {!! Form::close() !!}
	                @if ($errors->any())
	                	<div class="alert alert-danger"><p>{{ $errors->all()[0] }}</p></div>
	                	{{-- SWOOP: We know there will only be one error here. --}}
					@endif
				    @if (isset($data))
	                	@if ($data['size'])
	                	    <table class="table table-striped">
		                    	<tr>
		                       		<th>Name</th>
		                       		<th>Accounts / Services</th>
		                    		<th>Add</th>
		                    	</tr>
		                    	@foreach ($data['providers'] as $provider)
		                    		<tr>
		                    		<td> {{ $provider['name'] }} </td>
		                    		<td> Categories </td>
									<td><a href="/account/login/{{ $provider['id'] }}"><button type="button" class="btn btn-sm btn-warning">+</button></a></td>
									</tr>
								@endforeach
							</table>
						@else
							<div class="alert alert-warning">No matches found. Please search again.</div>
						@endif
					@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection