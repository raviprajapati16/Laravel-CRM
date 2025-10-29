<div class="hierarchy-item level-{{ $level }}">
    @if($level == 0)
        <strong>{{ $contact->email }}</strong>
    @else
        <div class="text-muted small">{{ $contact->email }}</div>
    @endif
</div>

{{-- Recursively display merged emails --}}
@if($contact->mergedContacts->count() > 0)
    @foreach($contact->mergedContacts as $merged)
        @include('contacts.partials.email_hierarchy', [
            'contact' => $merged, 
            'level' => $level + 1
        ])
    @endforeach
@endif