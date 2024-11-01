<div class="dropdown-menu add-member-drop member-assign" aria-labelledby="dropdownMenuButton" style="padding: 17px 16px !important;">
	<div class="add-member">
		<button class="close-btn-pop close-member">
			<span class="material-symbols-outlined">close</span>
		</button>
		<h4 class="mem-heading">Members</h4>
		<form>
			<div class="form-group">
				<input type="text" class="form-control" id="searchMember" aria-describedby="searchHelp" placeholder="Search members">
			</div>
		</form>
		<div class="card-members d-none">
			<h4 class="bm-heading">Card Members</h4>
			<div class="sletmem-prof">
				<ul class="card-member-assignment member-assignment"></ul>
			</div>
		</div>
		<div class="board-members">
			<h4 class="bm-heading">
				Board Members </h4>
			<div class="sletmem-prof">
				<ul class="board-members-list member-assignment">
{{--					@foreach($users as $user_key => $user)--}}
{{--						@php--}}
{{--							$fnl = strtolower($user['name'][0]);--}}
{{--							$user_id = (((( $user->id + 783 ) * 7 ) * 7 ) * 3).$user->created_at->timestamp.random_int(111,999);--}}
{{--						@endphp--}}
{{--						<li class="assign-unassign-members" id="assign-member-{{$user_id}}" data-id="{{$user_id}}" data-title="{{ $user->name }}">--}}
{{--                        <span class="member-avatar {{$fnl >= 'a' && $fnl <= 'e' ? "color1" :( $fnl >= 'f' && $fnl <= 'j' ? "color2" : ($fnl >= 'k' && $fnl <= 'o' ? "color3" : ($fnl >= 'p' && $fnl <= 't' ? "color4" : ($fnl >= 'u' && $fnl <= 'x' ? "color5" : "color6"))))}}" title="{{$user->name}}">{{ strtoupper(substr(implode('', array_map(function ($word) {return strtoupper($word[0]);}, explode(' ', $user['name']))), 0, 2)) }}</span>--}}
{{--							<div class="membr-list-name">{{ $user->name }}</div>--}}
{{--						</li>--}}
{{--					@endforeach--}}
				</ul>
			</div>
		</div>
	</div>
</div>
