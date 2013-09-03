{{#each predictions}}
	{{#if minutes}}
		<div class="row">
			<span class="route block">{{route}}</span>
			<span class="title block">{{stopTitle}}</span>
			<span class="direction">{{directionTitle}}</span>
			<span class="prediction block {{expected_class}}">{{minutes}} mintues</span>
		</div>	
	{{else}}
		<div class="row">The future is unknown!  ... and we had some problem finding predictions :( </div>	
		<div class="row">"I never think of the future - it comes soon enough." (Albert Einstein)</div>
	{{/if}}
{{/each}}