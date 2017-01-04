@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
               		<h4>You currently have no financial institutions added.</h4>
            		<p>The more institutions you add, the more you earn every month.</p>
            		<p>You can add your financial institutions such as banks, credit cards, retirement, mortgage, reward cards etc. in three simple steps.</p>
            		<p>Please click "Continue" to get started.</p>
            		
				</div>	
				<div class="panel-body">
					<p><a href="/account/link"><button type="button" class="btn btn-primary btn-lg btn-block btn-warning">Continue</button></a></p>
			<!--
	                <p>Resources for new users.</p>
                	<ul>
                		<li><a href="#">Screenshots</a></li>
                		<li><a href="#">About UAS-FIN</a></li>
                	</ul>
			-->
                </div>
            </div>
        </div>
    </div>
    <div align="center">Problems adding an institution? Please contact the helpdesk at 1-855-872-8673 (9am - 5pm PST) or by email: <a href="mailto:uashelp@usc.edu?subject=[via%20uasfin.usc.edu%20-%20Yodlee]">uashelp@usc.edu</a>.</div>
</div>
@endsection
