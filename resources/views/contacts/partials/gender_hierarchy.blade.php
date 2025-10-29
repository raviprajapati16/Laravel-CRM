<div class="hierarchy-item level-{{ $level }}">
    <span class="badge {{ $level == 0 ? 'bg-primary' : 'bg-secondary' }}">
        {{ ucfirst($contact->gender) }}
    </span>
</div>

{{-- Recursively display merged genders --}}
@if($contact->mergedContacts->count() > 0)
    @foreach($contact->mergedContacts as $merged)
        @include('contacts.partials.gender_hierarchy', [
            'contact' => $merged, 
            'level' => $level + 1
        ])
    @endforeach
@endif