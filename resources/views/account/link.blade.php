@extends('layouts.app')

@section('content')
<?php //dd($data['providers']); ?>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
            	<div class="alert alert-info">
            		<h4><strong>Step 1a: </strong><br/><br/>Select from the list of popular financial institutions or <button type="button" class="btn btn-primary btn-warning"><a href="/account/search"><strong>Search</strong></a></button></h4>
            	</div> 
                <div class="panel-body">
                	<div class="container">
	    				<div class="row row-centered" style="text-align:center;">
	        				<div class="col-xs-3 col-centered"><div class="item"><div class="content">
	        					<h5><strong>CREDIT CARDS</strong></h5>
	        					<a href="/account/add/643"><button type="button" class="btn btn-info btn-block">Chase Credit Card</button></a>
	        					<br/>
	        					<a href="/account/add/1503"><button type="button" class="btn btn-info btn-block">Citi Credit Card</button></a>
	        					<br/>
	        					<a href="/account/add/2856"><button type="button" class="btn btn-info btn-block">Capital One Credit Card</button></a>
	        					<br/>
	        					<a href="/account/add/12"><button type="button" class="btn btn-info btn-block">American Express Card</button></a>
	        					<br/>
	        					<a href="/account/add/2852"><button type="button" class="btn btn-info btn-block">Bank of America Credit Card</button></a>
	        					<br/>
	        					<a href="/account/add/5"><button type="button" class="btn btn-info btn-block">Wells Fargo Credit Card</button></a>
	        					<br/>
	        					<a href="/account/add/11"><button type="button" class="btn btn-info btn-block">Discover Credit Card</button></a>
	        					<br/>
	        					<a href="/account/add/10017"><button type="button" class="btn btn-info btn-block">Barclaycard Credit Card</button></a>
	        					<br/>
	        					<a href="/account/add/745"><button type="button" class="btn btn-info btn-block">Fifth Third Credit Card</button></a>
	        					<br/>
	        					<a href="/account/add/2383"><button type="button" class="btn btn-info btn-block">Sun Trust Credit Card</button></a>
	        					<br/>
	        				</div></div></div>

	        				<div class="col-xs-3 col-centered"><div class="item"><div class="content">
	        					<h5><strong>BANKS</strong></h5>
	        					<a href="/account/add/643"><button type="button" class="btn btn-info btn-block">JP Morgan Chase</button></a>
	        					<br/>
	        					<a href="/account/add/2852"><button type="button" class="btn btn-info btn-block">Bank of America</button></a>
	        					<br/>
	        					<a href="/account/add/1603"><button type="button" class="btn btn-info btn-block">Citi Bank</button></a>
	        					<br/>
	        					<a href="/account/add/5"><button type="button" class="btn btn-info btn-block">Wells Fargo</button></a>
	        					<br/>
	        					<a href="/account/add/3612"><button type="button" class="btn btn-info btn-block">HSBC</button></a>
	        					<br/>
	        					<a href="/account/add/524"><button type="button" class="btn btn-info btn-block">US Bank</button></a>
	        					<br/>
	        					<a href="/account/add/2162"><button type="button" class="btn btn-info btn-block">PNC Bank</button></a>
	        					<br/>
	        					<a href="/account/add/3646"><button type="button" class="btn btn-info btn-block">CapitalOne Bank</button></a>
	        					<br/>
	        					<a href="/account/add/4132"><button type="button" class="btn btn-info btn-block">TD Bank</button></a>
	        					<br/>
	        					<a href="/account/add/5060"><button type="button" class="btn btn-info btn-block">Citizens Bank</button></a>
	        					<br/>
	        				</div></div></div>

	        				<div class="col-xs-3 col-centered"><div class="item"><div class="content">
	        					<h5><strong>RETIREMENT / INVESTMENT</strong></h5>	
	        					<a href="/account/add/18091"><button type="button" class="btn btn-info btn-block">TIAA</button></a>
	        					<br/>
	        					<a href="/account/add/492"><button type="button" class="btn btn-info btn-block">Fidelity</button></a>
	        					<br/>
	        					<a href="/account/add/98"><button type="button" class="btn btn-info btn-block">Vanguard</button></a>
	        					<br/>
	        					<a href="/account/add/744"><button type="button" class="btn btn-info btn-block">E*Trade</button></a>
	        					<br/>
	        					<a href="/account/add/16255"><button type="button" class="btn btn-info btn-block">Scottrade</button></a>
	        					<br/>
	        					<a href="/account/add/291"><button type="button" class="btn btn-info btn-block">TD Ameritrade Inc.</button></a>
	        					<br/>
	        					<a href="/account/add/21"><button type="button" class="btn btn-info btn-block">Charles Schwab</button></a>
	        					<br/>
	        					<a href="/account/add/4120"><button type="button" class="btn btn-info btn-block">CapitalOne 360</button></a>
	        					<br/>
	        					<a href="/account/add/15459"><button type="button" class="btn btn-info btn-block">Wells Fargo Retirement</button></a>
	        					<br/>
	        					<a href="/account/add/1649"><button type="button" class="btn btn-info btn-block">Morgan Stanley</button></a>
	        				</div></div></div>
					    </div>
					</div>
					<div><p></p></div>
					<div style="text-align:center;">
						<a href="/account/search"><button type="button" class="btn btn-primary btn-lg btn-warning">Is your institution not listed above?&nbsp;&nbsp;Click here to Search</button></a>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
@endsection
