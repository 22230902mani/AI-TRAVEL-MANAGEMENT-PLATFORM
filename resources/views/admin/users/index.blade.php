@extends('layouts.admin')
@section('title','Admin — Users')
@section('content')
<div style="padding:2rem;max-width:1400px;margin:0 auto">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
        <h1 style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900"><i class="fas fa-users" style="color:var(--primary)"></i> User Management</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>
    <form method="GET" style="display:flex;gap:.75rem;margin-bottom:1.5rem">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name/email..." style="max-width:300px">
        <select name="role" class="form-control" style="max-width:180px">
            <option value="">All roles</option>
            @foreach(['traveler','admin','super_admin','travel_agency','trip_organizer'] as $r)
            <option value="{{ $r }}" {{ request('role')==$r?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$r)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </form>
    <div class="card" style="overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead><tr style="background:var(--surface2)">
                @foreach(['#','Name','Email','Role','Status','Joined','Actions'] as $h)
                <th style="padding:.85rem 1rem;text-align:left;font-size:.82rem;color:var(--muted);font-weight:600;border-bottom:1px solid var(--border)">{{ $h }}</th>
                @endforeach
            </tr></thead>
            <tbody>
            @foreach($users as $user)
            <tr style="border-bottom:1px solid var(--border)">
                <td style="padding:.75rem 1rem;font-size:.85rem;color:var(--muted)">{{ $user->id }}</td>
                <td style="padding:.75rem 1rem">
                    <div style="display:flex;align-items:center;gap:.5rem">
                        <img src="{{ $user->avatar_url }}" style="width:30px;height:30px;border-radius:50%">
                        <span style="font-weight:600;font-size:.9rem">{{ $user->name }}</span>
                    </div>
                </td>
                <td style="padding:.75rem 1rem;font-size:.85rem;color:var(--muted)">{{ $user->email }}</td>
                <td style="padding:.75rem 1rem">
                    @foreach($user->getRoleNames() as $role)
                    <span class="badge-pill badge-primary" style="font-size:.72rem">{{ str_replace('_',' ',$role) }}</span>
                    @endforeach
                </td>
                <td style="padding:.75rem 1rem">
                    <span class="badge-pill {{ ($user->is_active??true)?'badge-success':'badge-danger' }}" style="font-size:.72rem">{{ ($user->is_active??true)?'Active':'Inactive' }}</span>
                </td>
                <td style="padding:.75rem 1rem;font-size:.82rem;color:var(--muted)">{{ $user->created_at->format('M d, Y') }}</td>
                <td style="padding:.75rem 1rem">
                    <div style="display:flex;gap:.4rem">
                        <a href="{{ route('admin.users.show',$user) }}" class="btn btn-outline btn-sm"><i class="fas fa-eye"></i></a>
                        <form method="POST" action="{{ route('admin.users.toggle',$user) }}">@csrf
                            <button type="submit" class="btn btn-sm {{ ($user->is_active??true)?'btn-danger':'btn-primary' }}" style="padding:.3rem .6rem">
                                <i class="fas {{ ($user->is_active??true)?'fa-ban':'fa-check' }}"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top:1.25rem">{{ $users->links() }}</div>
</div>
@endsection
