<div class="hierarchy-item level-{{ $level }}">
    @if($level == 0)
        <strong>{{ $contact->customFields->firstWhere('id', $fieldId)?->pivot->value ?? '' }}</strong>
    @else
        <div class="text-muted small">{{ $contact->customFields->firstWhere('id', $fieldId)?->pivot->value ?? '' }}</div>
    @endif
</div>

{{-- Recursively display merged custom fields --}}
@if($contact->mergedContacts->count() > 0)
    @foreach($contact->mergedContacts as $merged)
        @include('contacts.partials.custom_field_hierarchy', [
            'contact' => $merged, 
            'fieldId' => $fieldId,
            'level' => $level + 1
        ])
    @endforeach
@endif