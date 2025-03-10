@extends('layouts.app')

@section('content')
<div class="container">
    <h1>All Activity Logs</h1>
    
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activities as $activity)
                        <tr>
                            <td>{{ $activity->user->name }}</td>
                            <td>{{ ucfirst($activity->action) }}</td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $activity->ip_address }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $activities->links() }}
        </div>
    </div>
</div>
@endsection
