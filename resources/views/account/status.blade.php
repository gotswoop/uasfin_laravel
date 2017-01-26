@extends('layouts.app')

@section('content')
<?php //dd($accounts); ?>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                	<div style="float: left">
                		<h4>Institution Adding Status</h4>	
					</div>
	                <div style="clear: both"></div>
                </div>		
                <div class="panel-body">

	                @if ( isset($refreshInfo) && isset($providerAccountId) )

	                	Status: {{ $refreshInfo['status'] }} <br/>
	                	Status Message: {{ $refreshInfo['statusMessage'] }} <br/>
	                	Additional Status: {{ $refreshInfo['additionalStatus'] }} <br/>
	                	<a href="/account/status/{{ $providerAccountId }}">Refresh Again</a>
	                	<br/><br/>
	                	<?php print_r($refreshInfo); ?>


	                @else
						{{-- */ $i = 1 /* --}}
	                    <table class="table table-striped">
	                    	<tr>
	                    		<th>#</th>
	                    		<th>Institution Name</th>
								<th>Status Code</th> 
	                    		<th>Status (Reason)</th>
	                    		<!--
	                    		<th>Last Activity</th>
	                    		-->
	                    		<th>Action</th>
	                    		
	                    	</tr>	
	                    	@foreach ($accounts as $account)
	                    		<tr>
	                    		<td> {{ $i++ }}. </td>
	                    		<td style="font-weight: bold"> {{ $account['id'] }}</td>
	                    		<td> {{ $account['refreshInfo']['statusCode'] }}</td>
	                    		<td> {{ $account['refreshInfo']['status'] }} ( {{ $account['refreshInfo']['statusMessage'] }} 
	                    			@if ( isset($account['refreshInfo']['additionalStatus']) ) 
	                    				- {{ $account['refreshInfo']['additionalStatus'] }} 
	                    			@endif
	                    			)
	                    		</td>
	                    		{{-- 
	                    		<td>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($account['refreshInfo']['lastRefreshed']))->diffForHumans() }} </td>
	                    		--}}
								@if ($account['refreshInfo']['status'] == 'SUCCESS')
									<td> <a href="/account/removeProviderAccount/{{ $account['id'] }}">Remove Institution</a></td>
								@else
									<td><a href="/account/removeProviderAccount/{{ $account['id'] }}">Clear</a>&nbsp;&nbsp;&nbsp;<a href="/account/add/{{ $account['providerId'] }}">Try Again<a/></td>
								@endif
	                    	@endforeach
	                   	</table>
                   	@endif
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
