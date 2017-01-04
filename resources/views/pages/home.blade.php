@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img class="img-responsive" src='/images/uas_logo_bw.png' onmouseover="this.src='/images/uas_logo_c.png';" onmouseout="this.src='/images/uas_logo_bw.png';" />
                        </div>        
                        <div class="col-md-6">
                            <h4>Welcome to the Understanding America Study - Personal FINance Management (UAS-FIN) Tool</h4>
                            <hr/>
                            <p>We are interested in how Americans spend their money and how they are doing financially. We have created this custom made financial management web-site UAS-FIN. The web-site has been developed in collaboration with one of the biggest financial management service companies in the world: <a href="https://www.yodlee.com/company/why-yodlee" target="_blank"><strong>Yodlee</strong></a>. For instance, Yodlee provides services to 12 of the 20 largest banks in the United States.</p>

							<p>We will <strong>not</strong> have access to your passwords or any other identifying information; this information will be safeguarded by Yodlee. We will use the data in the same way we use surveys you participate in: to make summary tables or graphs to better understand how Americans are doing. Just like the information you provide through surveys, you will be compensated for the information that you share with us.</p>

							<p>You can change your mind at any time. Even if you say yes now, this does not obligate you for the future; you can withdraw at any time.</p>
							<hr/>
                            <h5>Already have an account? Click the Login button</h5>
                            <p><a href="/login"><button type="button" class="btn btn-primary btn-md">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Login&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button></a></p>
                            <h5>New to UAS-FIN? Click Sign Up to create an account</h5>
							<p><a href="/register"><button type="button" class="btn btn-primary btn-md">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sign Up&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button></a></p>
							<br/>
                        </div>        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
