@extends('layouts.app')

@section('content')
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
                		<h5>Add more institutions to get a better financial picture.</h5>
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
                    		<th>Account  Name</th>
                    		{{-- <th>Type 1</th> --}}
                    		<th>Type</th>
                    		<th>Balance</th>
                    		<!--
                    		<th>Last Updated</th>
                    		<th>Refresh</th>
                    		-->
                    	</tr>	
                    	
                    	@foreach ($accounts as $account)
                    		@if (array_key_exists('balance', $account))
	                    		<tr>
	                    		<td> {{ $i++ }}. </td>
	                    		<td style="font-weight: bold"><a href="/account/{{ $account['id'] }}/?container={{ $account['CONTAINER'] }}"> {{ $account['providerName'] }} </a></td>
	                    		@if (array_key_exists('accountName', $account))
	                    			<td> {{ $account['accountName'] }} </td>
	                    		@else
	                    			<td>&nbsp;</td>
	                    		@endif
	                    		<td> {{ $account['accountType'] }} </td>
								{{-- <td> {{ \Html::link('/account/'.$account['id'], 'test', array('container' => 'creditCard') ) }} </td> --}}

								{{--*/ $color = 'black' /*--}}
								{{--*/ $neg = '' /*--}}
								@if ($account['balance']['amount'])
									{{--*/ $color = $account['isAsset'] ? 'green' : 'red' /*--}}
									{{--*/ $neg = $account['isAsset'] ? '' : '-' /*--}}
								@endif
								<td style="font-weight: bold; color: {{ $color }}">
								@if ($account['balance']['currency'] == 'USD')
									 {{ $neg }}$ {{ number_format($account['balance']['amount'],2) }}
								@else
									{{ number_format($account['balance']['amount'],2) }} ({{ $account['balance']['currency'] }})
								@endif
								</td>
								<!-- 
								<td>{{ \Carbon\Carbon::createFromTimeStamp(strtotime($account['lastUpdated']))->diffForHumans() }} </td>
								<td align="center"><a href="/account/refresh/{{ $account['providerAccountId'] }}"><img src="/images/refresh.png"></a></td>
								-->
							@endif
						@endforeach
                   	</table>

                </div>

            </div>
            <div class="alert alert-info"><p>*Tip: The more financial institutions you add, the more you earn every month. <a href="/account/link"><strong>Click here</strong></a> to add your financial institution</p></div>
        </div>
    </div>
    
</div>
@endsection
