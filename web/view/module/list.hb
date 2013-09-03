{{#each predictions}}
	{{#if minutes}}
		<div class="row">
			<div>
				<span class="route block {{expected_class}}">{{route}}</span>
			</div>
			<div>
				<ul>
					<li class="prediction"><h3>{{minutes}} minutes</h3></li>
					<li class="title">{{stopTitle}}</li>
					<li class="direction">{{directionTitle}}</li>
				</ul>
			</div>
		</div>	
	{{else}}
		<div class="row">
			<div>The future is unknown!  ... and we had some problem finding predictions :( </div>	
			<br>
			<div>"I never think of the future - it comes soon enough." (Albert Einstein)</div>
		</div>
	{{/if}}
{{/each}}