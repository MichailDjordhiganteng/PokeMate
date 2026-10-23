@props(['user'])
<span {{ $attributes->class(['user-avatar']) }}>
    @if($user->trainerProfile?->avatar)<img src="{{ asset('storage/'.$user->trainerProfile->avatar) }}" alt="Avatar {{ $user->full_name }}">
    @else<span>{{ mb_strtoupper(mb_substr($user->full_name, 0, 1)) }}</span>@endif
</span>
