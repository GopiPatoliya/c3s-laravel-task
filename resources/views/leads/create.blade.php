@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add New Lead</h2>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('leads.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}">
                    @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="">Select Status</option>
                        <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ old('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="converted" {{ old('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                        <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Source *</label>
                    <select name="source" class="form-select @error('source') is-invalid @enderror">
                        <option value="">Select Source</option>
                        <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                        <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                        <option value="social_media" {{ old('source') == 'social_media' ? 'selected' : '' }}>Social Media</option>
                        <option value="cold_call" {{ old('source') == 'cold_call' ? 'selected' : '' }}>Cold Call</option>
                        <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('source') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Assign To</label>
                    <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                        <option value="">-- Unassigned --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('assigned_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save Lead</button>
                <a href="{{ route('leads.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Remove 'is-invalid' class when user starts typing or selects a new value
    const invalidInputs = document.querySelectorAll('.is-invalid');
    invalidInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
        input.addEventListener('change', function() {
            this.classList.remove('is-invalid');
        });
    });
});
</script>
@endsection
