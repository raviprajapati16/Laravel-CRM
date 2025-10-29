<div class="hierarchy-item level-{{ $level }}">
    @if($level == 0)
        <strong>{{ $contact->phone }}</strong>
    @else
        <div class="text-muted small">{{ $contact->phone }}</div>
    @endif
</div>

{{-- Recursively display merged phones --}}
@if($contact->mergedContacts->count() > 0)
    @foreach($contact->mergedContacts as $merged)
        @include('contacts.partials.phone_hierarchy', [
            'contact' => $merged, 
            'level' => $level + 1
        ])
    @endforeach
@endif