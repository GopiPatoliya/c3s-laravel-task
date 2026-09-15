@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Lead Details</h2>
        <div>
            <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-warning">Edit Lead</a>
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Name</div>
                <div class="col-md-9">{{ $lead->name }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Email</div>
                <div class="col-md-9">{{ $lead->email ?: 'N/A' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Phone</div>
                <div class="col-md-9">{{ $lead->phone ?: 'N/A' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Company Name</div>
                <div class="col-md-9">{{ $lead->company_name ?: 'N/A' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Status</div>
                <div class="col-md-9">{!! leadStatusBadge($lead->status) !!}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Source</div>
                <div class="col-md-9">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Assigned To</div>
                <div class="col-md-9">{{ $lead->assignedUser ? $lead->assignedUser->name : 'Unassigned' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Notes</div>
                <div class="col-md-9">{!! nl2br(e($lead->notes)) ?: 'N/A' !!}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Created At</div>
                <div class="col-md-9">{{ $lead->created_at->format('M d, Y H:i') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 fw-bold">Last Updated At</div>
                <div class="col-md-9">{{ $lead->updated_at->format('M d, Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
