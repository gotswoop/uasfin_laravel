@extends('layouts.app')

@section('content')
<?php //dd($data['providers']); ?>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
            	@if (isset($data))
            		@if ($data['size'])
                		<div class="alert alert-info"><h4><strong>Step 2: </strong></h4><h4>Click the <button type="button" class="btn btn-sm btn-warning">Add This Account</button> next to the financial institution you wish to add.</h4></div>
                	@else
                		<div class="alert alert-info"><h4><strong>Step 1b: </strong><br/><br/>Enter the name your financial institution you wish to add and click "Search". <br/><br/>For example, "Citi Credit Cards".</h4></div> 
                	@endif
                @else 
                	<div class="alert alert-info"><h4><strong>Step 1b: </strong><br/><br/>Enter the name your financial institution you wish to add and click "Search". <br/><br/>For example, "Citi Credit Cards".</h4></div> 
				@endif	
                <div class="panel-body">
                	{!! Form::open() !!}
                	<div class="form-group">
						{!! Form::text('search', null, ['class' => 'form-control']) !!}	
					</div>
					<div class="form-group">
						{!! Form::submit('Search', ['class' => 'btn btn-primary btn-lg btn-block btn-warning']) !!}	
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
		                       		<th>Institution Name</th>
		                       		<th>Accounts / Services</th>
		                    		<th>&nbsp;</th>
		                    	</tr>
		                    	@foreach ($data['providers'] as $provider)
		                    		@if (isset($provider['countryISOCode']))
			                    		@if ($provider['countryISOCode'] == 'US')
				                    		<tr>
				                    		<td> <a href="/account/add/{{ $provider['id'] }}">{{ $provider['name'] }} </a></td>
				                    		<?php 
				                    			if (isset($provider['containerNames'])) {
				                    				$categories = '';
				                    				foreach($provider['containerNames'] as $category) {
				                    					$categories .= $category.', ';
				                    				}
				                    			}
				                    		?>
				                    		<td> {{ rtrim($categories, ', ') }}</td>
											<td><a href="/account/add/{{ $provider['id'] }}"><button type="button" class="btn btn-sm btn-warning">Add This Account</button></a></td>
											</tr>
										@endif
									@else 
										<tr>
			                    		<td> <a href="/account/add/{{ $provider['id'] }}">{{ $provider['name'] }} </a></td>
			                    		<?php 
			                    			if (isset($provider['containerNames'])) {
			                    				$categories = '';
			                    				foreach($provider['containerNames'] as $category) {
			                    					$categories .= $category.', ';
			                    				}
			                    			}
			                    		?>
			                    		<td> {{ rtrim($categories, ', ') }}</td>
										<td><a href="/account/add/{{ $provider['id'] }}"><button type="button" class="btn btn-sm btn-warning">Add This Account</button></a></td>
										</tr>
									@endif
								@endforeach
							</table>
							<div class="alert alert-warning">Could not find your institution? Please search again with just one word or a different combination of words.</div>
						@else
							<div class="alert alert-warning">No matches found. Please search again with just one word or a different combination of words.</div>
						@endif
					@endif
                </div>
            </div>
        </div>
    </div>
    <div align="center">Problems adding an institution? Please contact the helpdesk at 1-855-872-8673 (9am - 5pm PST) or by email: <a href="mailto:uashelp@usc.edu?subject=[via%20uasfin.usc.edu%20-%20Yodlee]">uashelp@usc.edu</a>.</div>
</div>
@endsection
