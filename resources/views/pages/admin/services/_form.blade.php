@php
    $service = $service ?? null;
    $whatsIncluded = old('whats_included', $service->whats_included ?? []);
    if (is_string($whatsIncluded)) {
        $whatsIncluded = json_decode($whatsIncluded, true) ?: [];
    }
@endphp

<div class="form-group">
    <label for="name">Service Name <span class="text-danger">*</span></label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name ?? '') }}" placeholder="e.g. Regular Home Cleaning" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="description">Description</label>
    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Enter service description">{{ old('description', $service->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <div class="d-flex align-items-center mb-2">
        <label class="mb-0 mr-1">What's Included</label>
        <i class="fas fa-info-circle text-muted" data-toggle="tooltip" data-placement="top" title="Add features/tasks that are included in this cleaning service." style="cursor: pointer; font-size: 13px;"></i>
    </div>

    <div id="whats-included-list" class="whats-included-list">
        @if (!empty($whatsIncluded) && is_array($whatsIncluded))
            @foreach ($whatsIncluded as $index => $item)
                <div class="included-feature-row">
                    <input type="text" name="whats_included[]" class="form-control" value="{{ $item }}" placeholder="e.g. Dusting and wiping all reachable surfaces">
                    <button type="button" class="included-feature-remove-btn js-remove-included-feature" title="Delete feature">
                        <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>
                </div>
            @endforeach
        @else
            <div class="included-feature-row">
                <input type="text" name="whats_included[]" class="form-control" value="" placeholder="e.g. Dusting and wiping all reachable surfaces">
                <button type="button" class="included-feature-remove-btn js-remove-included-feature" title="Delete feature">
                    <i class="fas fa-trash" aria-hidden="true"></i>
                </button>
            </div>
        @endif
    </div>

    <div class="mt-2">
        <button type="button" class="btn btn-sm btn-outline-primary" id="js-add-included-feature" title="Add More">
            <i class="fas fa-plus mr-1" aria-hidden="true"></i> Add More
        </button>
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4 mb-0">
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
