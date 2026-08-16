@php
    $service = $service ?? null;
@endphp

<div class="form-group">
    <label for="name">Service Name <span class="text-danger">*</span></label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name ?? '') }}" placeholder="e.g. Regular Home Cleaning">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Enter service description">{{ old('description', $service->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label for="status">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
            <option value="active" {{ old('status', $service->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $service->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
