@extends('layouts.app')

@section('content')
<?php //dd($summary); ?>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 style="color:#330000;">{{ $summary['providerName'] }} - {{ $summary['accountName']}}</h3>
                	{{--*/ $color = 'black' /*--}}
                	{{--*/ $neg = '' /*--}}
                	@if ($summary['balance']['amount'])
						{{--*/ $color = ($summary['isAsset']) ? 'green' : 'red' /*--}}
						{{--*/ $neg = $summary['isAsset'] ? '' : '-' /*--}}
					@endif
					<h4 style="font-weight: bold; color: {{ $color }}"> Balance: 
                	@if ($summary['balance']['currency'] == 'USD')
						{{ $neg }}$ {{ number_format($summary['balance']['amount'],2) }}
					@else
						{{ $neg }} {{ number_format($summary['balance']['amount'],2) }} ({{ $summary['balance']['currency'] }})
					@endif	
                	</h4>
                	<h5>Type: {{ $summary['accountType']}} </h5>
                	<h5>Pending and Cleared Transactions (updated {{ \Carbon\Carbon::createFromTimeStamp(strtotime($summary['lastUpdated']))->diffForHumans() }})</h5>
                </div>
	            <div class="panel-body">
					<table class="table table-striped">
                    	<tr>
                       		<th>Status</th>
                       		<th>Date</th>
                    		<th>Description</th>
                    		<th>Category</th>
                    		<th>Amount</th>
                    	</tr>
                    	@if ($transactions) 
	                    	@foreach ($transactions as $transaction)
	                    		<tr>
	                    		<td> {{ mb_strtolower($transaction['status']) }} </td>
	                    		<td> {{ $transaction['date'] }} </td>
	                    		<td> {{ $transaction['description']['original'] }} </td>
								<td> {{ $transaction['category'] }} </td>
								{{--*/ $color = ($transaction['baseType'] == 'CREDIT') ? 'green' : 'red' /*--}}
								{{--*/ $neg = ($transaction['baseType'] == 'CREDIT') ? '' : '-' /*--}}
								<td style="font-weight: bold; color: {{ $color }}">
								@if ($transaction['amount']['currency'] == 'USD')
									 {{ $neg }}$ {{ number_format($transaction['amount']['amount'],2) }}
								@else
									{{ $neg }} {{ number_format($transaction['amount']['amount'],2) }} ({{ $transaction['amount']['currency'] }})
								@endif
								</td>
								</tr>
							@endforeach
						@else 
							<tr><td colspan="5"><div class="alert alert-warning">No recent transactions found for this account</div></td></tr>
                    	@endif	
                   	</table>
                </div>
            </div>
        </div>
    </div>
    <div align="center">
    	<p class="small">PAI-AI#: {{ $summary['providerAccountId'] }}-{{ $accountId }}</p>
    </div>
</div>
@endsection