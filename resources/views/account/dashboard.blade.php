@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                	<div style="float: left">Account Summary</div>
                	<div style="float: right"><button type="button" class="btn btn-sm btn-info">Refresh Accounts</button></div>
                	<div style="float: right">&nbsp;</div>
                	<div style="float: right"><a href="/account/search"><button type="button" class="btn btn-sm btn-warning">Add Account</button></a></div>
                	<div style="clear: both"></div>
                </div>		
                <div class="panel-body">
					{{-- */ $i = 1 /* --}}
                    <table class="table table-striped">
                    	<tr>
                    		<th>#</th>
                    		<th>Institution</th>
                    		<th>Account Name</th>
                    		<th>Type 1</th>
                    		<th>Type 2</th>
                    		<th>Balance</th>
                    	</tr>	
                    	@foreach ($accounts as $account)
                    		<tr>
                    		<td> {{ $i++ }}. </td>
                    		<td style="font-weight: bold"><a href="/account/{{ $account['id'] }}"> {{ $account['providerName'] }} </a></td>
							<td> {{ $account['accountName'] }} </td>
                    		<td> {{ $account['CONTAINER'] }} </td>
							<td> {{ $account['accountType'] }} </td>

							{{--*/ $color = 'black' /*--}}
							@if ($account['balance']['amount'])
								{{--*/ $color = $account['isAsset'] ? 'green' : 'red' /*--}}
							@endif
							<td style="font-weight: bold; color: {{ $color }}">
							@if ($account['balance']['currency'] == 'USD')
								 $ {{ number_format($account['balance']['amount'],2) }}
							@else
								{{ number_format($account['balance']['amount'],2) }} ({{ $account['balance']['currency'] }})
							@endif
							</td>

						@endforeach
                   	</table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection