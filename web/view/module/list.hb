<div id="navigation">
    <a class="btn reload" href="javascript:;"><i class="icon-repeat"></i> Reload</a>
    <a class="btn inbound" href="javascript:;"><i class="icon-arrow-down"></i> Inbound</a>
    <a class="btn outbound" href="javascript:;"><i class="icon-arrow-up"></i> Outbound</a>
</div>
{{#each predictions}}
	<div class="wrap">
		<div>
			<span class="route block {{expected_class}}">{{route}}</span>
		</div>
		<div>
			<ul>
				<li class="prediction"><h3>{{minutes}} minutes</h3></li>
				<li class="title">{{stopTitle}}</li>
				<li class="direction"><i class="{{arrow_class}}"></i> {{directionTitle}}</li>
			</ul>
		</div>
		<div class="clear"></div>
	</div>	
{{else}}
	<div class="wrap">
		<p>
			<i class="icon-frown icon-2x icon-muted"></i>
			The future is unknown!  ... and we had some problem finding predictions
		</p>
		<br>
		<p>
			<i class="icon-quote-left icon-2x pull-left icon-muted"></i>
			I never think of the future - it comes soon enough.
			<br>
			- Albert Einstein
		</p>
	</div>
{{/each}}