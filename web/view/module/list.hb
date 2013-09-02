
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
{{else}}
	<div>No predictions.  Ohrly?  Yrly -__- </div>	
{{/each}}

