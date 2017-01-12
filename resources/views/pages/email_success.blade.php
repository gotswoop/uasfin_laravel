@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
               		<h4>Message Received!</h4>
            		</div>	
				<div class="panel-body">
				    <p>Thank you for contacting us. We will get back to you shortly.</p>
				   	<p><a href="/account/dashboard"><button type="button" class="btn btn-md btn-info">Go to Dashboard</button></a></p>
	            </div>
            </div>
        </div>
    </div>
</div>
@endsection