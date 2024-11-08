<div>
	<div class="flex flex-col gap-3">
		<label for="search">Search Employee</label>
		<input
			wire:model.live.debounce.500ms="search"
			type="text"
			id="search"
			class="form-input pr-10"
			placeholder="Search Employee..."
		>
	</div>
	@if($employees)
		<ul class="list-none bg-gray-100">
			@foreach ($employees as $employee)
				<li
					wire:click="setEmployee('{{ $employee->id }}')"
					class="px-4 py-2 hover:bg-gray-100 cursor-pointer
					{{ !$loop->first ? 'border-t' : '' }} {{ !$loop->last ? 'border-b' : '' }}"
				>
					{{ $employee->name }}
				</li>
			@endforeach
		</ul>
	@endif
</div>