 <div class="container">
     <br/>
     <img alt="USC" title="USC" src="/images/cesr_logo.png">
     <br/><br/>
</div>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
            <span><img alt="USC" title="USC" height="26px" src="/images/uas_name.png">
             - Personal <strong>FIN</strong>ance Management</span>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <!--
            <ul class="nav navbar-nav">
                <li><a href="{{ url('/dashboard') }}">My Dashboard</a></li>
            </ul>
            -->

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                	@if ( (Request::path() != 'login') && (Request::path() != 'register') && (Request::path() != 'password/reset') )
                		<li><a href="{{ url('/login') }}"><button type="button" class="btn btn-primary btn-md">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Login&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button></a></li>
                		<li><a href="{{ url('/register') }}"><button type="button" class="btn btn-primary btn-md">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sign Up&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button></a></li>
                	@else 
                		<li><a href="{{ url('/') }}"><button type="button" class="btn btn-primary btn-md">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button></a></li>
                	@endif
                @else
                	<li><a href="{{ url('/account/dashboard') }}"><button type="button" class="btn btn-primary btn-md">&nbsp;Dashboard&nbsp;</button></a></li>
                    <li><a href="{{ url('/contact') }}"><button type="button" class="btn btn-primary btn-md">&nbsp;Report Issue&nbsp;</button></a></li>
                    <li><a href="{{ url('/logout') }}"><button type="button" class="btn btn-primary btn-md">&nbsp;{{ substr(Auth::user()->firstName, 0, 1) .' '.Auth::user()->lastName }} (Logout)&nbsp;</button></a></li>
                @endif
        	</ul>
        </div>
	</div>
</nav>
