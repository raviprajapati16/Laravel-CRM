<div class="hierarchy-item level-{{ $level }}">
    @if($level == 0)
        {{-- Primary Contact --}}
        <strong>{{ $contact->name }}</strong>
        <div class="text-muted small">Primary Contact</div>
    @else
        {{-- Merged Contact --}}
        <div class="d-flex align-items-center">
            @if($contact->profile_image)
                <img src="{{ asset('storage/' . $contact->profile_image) }}" alt="Profile Image" width="25" class="img-thumbnail mr-2">
            @endif
            <div>{{ $contact->name }}</div>
            <span class="badge bg-warning text-dark merge-badge ms-2">Merged</span>
            @if($level > 1)
                <span class="badge bg-info text-dark merge-badge ms-1">Level {{ $level }}</span>
            @endif
        </div>
    @endif
</div>

{{-- Recursively display merged contacts --}}
@if($contact->mergedContacts->count() > 0)
    @foreach($contact->mergedContacts as $merged)
        @include('contacts.partials.contact_hierarchy', [
            'contact' => $merged, 
            'level' => $level + 1
        ])
    @endforeach
@endif