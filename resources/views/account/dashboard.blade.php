@extends('layouts.app')

@section('content')
<?php  //dd($netWorth); ?>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                	{{--*/ $color = ($netWorth['total'] > 0) ? 'green' : 'red' /*--}}
                	{{--*/ $neg = ($netWorth['total'] > 0 ) ? '' : '-' /*--}}
                	
                	<div style="float: left">
                		<h2 style="color:#330000;">Net Worth: <font style="color: {{ $color }} ">{{ $neg }}$ {{ number_format(abs($netWorth['total']),2) }} </font></h2>
                		<h4>&nbsp;&nbsp;&nbsp;&nbsp;Assets: <font style="color:green">$ {{ number_format($netWorth['assets'],2) }} </font></hh5>
                		<h4>&nbsp;&nbsp;&nbsp;&nbsp;Liabilities: <font style="color:red">- $ {{ number_format($netWorth['liabilities'],2) }} </font></h4>
                		<h5>You have <strong>{{ $netWorth['active_institutions'] }}</strong> institutions linked. Add more institutions to get a better financial picture.</h5>
					</div>
					<div style="float: right">
						<div>&nbsp;</div>
						<div><a href="/account/link"><button type="button" class="btn btn-md btn-warning">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Add More Institutions&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button></a></div>
	                	<!--
	                	<div>&nbsp;</div>
	                	<div><a href="/account/refresh"><button type="button" class="btn btn-md btn-info">Refresh Accounts</button></a></div>
	                	-->
	                </div>
	                <div style="clear: both"></div>
                </div>		
                <div class="panel-body">
					{{-- */ $i = 1 /* --}}
                    <table class="table table-striped">
                    	<tr>
                    		<th>#</th>
                    		<th>Financial Institution</th>
                    		<th>Account Type</th>
                    		<th>Account Type</th>
                    		<th>Balance</th>
                    	</tr>	

							{{--*/ $i = 1; /*--}}
                   	        @foreach ($accounts as $providerAccountId => $account_)

                   	        <?php 
	                    		$accounts = $account_['accounts'];
	                    		$providerName = $account_['providerName'];
	                    		$status = $account_['status'];
                    		?>

                    		{{--*/ $j = 'a'; /*--}}
				       		@foreach ($accounts as $account )
				       		
				       		<tr>
                    		<td> {{ $i }}{{ $j++ }}. </td>
                    		<td style="font-weight: bold"><a href="/account/details/{{ $account['id'] }}/?container={{ $account['CONTAINER'] }}">{{ $providerName }} </a>
                    		@if ( $status == 'FAILED')
                    			&nbsp; <a href="#" class="text-danger">INACTIVE</a>
                    		@endif
                    		</td>
                    		<td> {{ ucwords(strtolower($account['accountName'])) }} </td>
                    		<td> {{ ucwords(strtolower($account['accountType'])) }} </td>
							{{--*/ $color = 'black' /*--}}
							{{--*/ $neg = '' /*--}}
							@if ($account['balanceAmount'])
								{{--*/ $color = $account['isAsset'] ? 'green' : 'red' /*--}}
								{{--*/ $neg = $account['isAsset'] ? '' : '-' /*--}}
							@endif
							<td style="font-weight: bold; color: {{ $color }}">
							@if ($account['balanceCurrency'] == 'USD')
								 {{ $neg }}$ {{ number_format($account['balanceAmount'],2) }}
							@else
								{{ $neg }}{{ number_format($account['balanceAmount'],2) }} ({{ $account['balanceCurrency'] }})
							@endif
							</td>

							{{-- <td>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($account['lastUpdated']))->diffForHumans() }} </td> --}}
							{{-- <td align="center"><a href="/account/refresh/{{ $account['providerAccountId'] }}"><img src="/images/refresh.png"></a></td> --}}

						@endforeach
						{{--*/ $i++; /*--}}	
						@endforeach
                   	</table>

                </div>

            </div>
            <div class="alert alert-info"><p>*Tip: The more financial institutions you add, the more you earn every month. <a href="/account/link"><strong>Click here</strong></a> to add your financial institution</p></div>
        </div>
    </div>
    
</div>
@endsection
