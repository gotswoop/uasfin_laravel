@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">ACCOUNT NAME - Balance ($xx.xxx)<br/>Pending and Cleared Transactions</div>
	               <div class="panel-body">
					<table class="table table-striped">
                    	<tr>
                       		<th>Status</th>
                       		<th>Date</th>
                    		<th>Description</th>
                    		<th>Category</th>
                    		<th>Amount</th>
                    	</tr>	
                    	@foreach ($transactions as $transaction)
                    		<tr>
                    		<td> {{ mb_strtolower($transaction['status']) }} </td>
                    		<td> {{ $transaction['date'] }} </td>
                    		<td> {{ $transaction['description']['original'] }} </td>
							<td> {{ $transaction['category'] }} </td>
							<td style="font-weight: bold; color: {{ ( $transaction['baseType'] == 'CREDIT') ? 'green' : 'black' }}">
							@if ($transaction['amount']['currency'] == 'USD')
								 $ {{ number_format($transaction['amount']['amount'],2) }}
							@else
								{{ number_format($transaction['amount']['amount'],2) }} ({{ $transaction['amount']['currency'] }})
							@endif
							</td>
							</tr>
						@endforeach
                   	</table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection