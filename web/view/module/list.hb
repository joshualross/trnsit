
{{#if predictions}}
	{{#each predictions}}
		<div class="row-fluid">
			<div class="span6">
				{{route}} - {{stopTitle}}
				<br>
				{{directionTitle}}
				<div class="row-fluid">
					<div class="span5 offset1">
						{{minutes}} mintues		
					</div>
				</div>
			</div>
		</div>	
	{{/each}}
{{else}}
	<div>The future is unknown!  ... and we had some problem finding predictions :( </div>	
	<div>"I never think of the future - it comes soon enough." (Albert Einstein)</div>
{{/if}}

