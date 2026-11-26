@extends('admin.layout', ['tab' => 'members', 'pageTitle' => $member->full_name.' | Admin area'])

@section('panel')
    <div class="x-cols">
        <div class="x-card">
            <h2 class="x-card-h">{{ $member->full_name }}</h2>
            <dl class="x-facts x-small">
                <div><dt>Status</dt><dd>{{ $member->career_stage ?: 'Not given' }}</dd></div>
                <div><dt>Institution</dt><dd>{{ $member->institution ?: 'Not given' }}</dd></div>
                <div><dt>Country</dt><dd>{{ $member->country ?: 'Not given' }}</dd></div>
                <div><dt>Member since</dt><dd>{{ $member->approved_at->format('j F Y') }}</dd></div>
                @if ($member->membership_end_requested_at)
                    <div><dt>End of membership</dt><dd>Asked to end the membership on {{ $member->membership_end_requested_at->format('j F Y') }}</dd></div>
                @endif
            </dl>
        </div>

        @can('administer')
            <form class="x-card" method="POST" action="{{ route('admin.members.roles', $member) }}" novalidate>
                @csrf
                @method('PUT')
                <div class="x-row x-between" style="margin-bottom:.75rem">
                    <h2 class="x-card-h" style="margin:0">Rights of {{ $member->full_name }}</h2>
                    <a class="x-link" href="{{ route('admin.members.index') }}">Close</a>
                </div>
                @php
                    $held = $member->roles->map(fn ($r) => $r->role)->all();
                    $rolesError = $errors->first('roles') ?: $errors->first('roles.*');
                @endphp
                <fieldset class="x-fieldset" @if ($rolesError) aria-describedby="f-roles-err" @endif>
                    <legend class="x-vh">Roles</legend>
                    @foreach (\App\Enums\Role::cases() as $role)
                        <label class="x-check" for="role-{{ $role->value }}">
                            <input type="checkbox" id="role-{{ $role->value }}" name="roles[]" value="{{ $role->value }}"
                                @checked(in_array($role, $held, true)) @disabled($role === \App\Enums\Role::Member)>
                            <span><strong>{{ $role->label() }}</strong><br><small class="x-muted">{{ $role->description() }}</small></span>
                        </label>
                    @endforeach
                    @if ($rolesError)
                        <p class="x-err" id="f-roles-err" role="alert">{{ $rolesError }}</p>
                    @endif
                </fieldset>
                <button class="btn btn-primary" type="submit" style="margin-top:1rem">Save the rights</button>
            </form>
        @else
            <div class="x-card">
                <h2 class="x-card-h">Rights of {{ $member->full_name }}</h2>
                <p class="x-small">{{ $member->roles->map(fn ($r) => $r->role->label())->implode(', ') }}</p>
            </div>
        @endcan
    </div>
@endsection
