<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover align-middle" id="contactsTable">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    @foreach ($customFields as $field)
                        @if ($field->show_on_table)
                            <th>{{ $field->name }}</th>
                        @endif
                    @endforeach
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contact)
                    @if(!$contact->merged_into_id) {{-- Only show master contacts --}}
                    <tr>
                        <td>
                            @if($contact->profile_image)
                            <img src="{{ asset('storage/' . $contact->profile_image) }}" alt="Profile Image" width="50" class="img-thumbnail mr-2">
                            @endif
                            {{ $contact->name }}
                            
                            {{-- Display merged contacts under this master --}}
                            @foreach($contact->mergedContacts as $merged)
                                <div class="merged-contact">
                                    @if($merged->profile_image)
                                    <img src="{{ asset('storage/' . $merged->profile_image) }}" alt="Profile Image" width="30" class="img-thumbnail mr-2">
                                    @endif
                                    {{ $merged->name }}
                                    <span class="badge badge-secondary text-black">Merged</span>
                                </div>
                            @endforeach
                        </td>
                        <td>
                            {{ $contact->email }}
                            @foreach($contact->mergedContacts as $merged)
                                <div class="merged-contact">{{ $merged->email }}</div>
                            @endforeach
                        </td>
                        <td>
                            {{ $contact->phone }}
                            @foreach($contact->mergedContacts as $merged)
                                <div class="merged-contact">{{ $merged->phone }}</div>
                            @endforeach
                        </td>
                        <td>
                            {{ ucfirst($contact->gender) }}
                            @foreach($contact->mergedContacts as $merged)
                                <div class="merged-contact">{{ ucfirst($merged->gender) }}</div>
                            @endforeach
                        </td>
                        @foreach($customFields as $field)
                            @if ($field->show_on_table)
                            <td>
                                {{ $contact->customFields->firstWhere('id', $field->id)?->pivot->value ?? '' }}
                                @foreach($contact->mergedContacts as $merged)
                                    <div class="merged-contact">
                                        {{ $merged->customFields->firstWhere('id', $field->id)?->pivot->value ?? '' }}
                                    </div>
                                @endforeach
                            </td>
                            @endif
                        @endforeach
                        <td>
                            <button class="btn btn-sm btn-primary edit-contact" data-id="{{ $contact->id }}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteForm" data-id="{{ $contact->id }}">Delete</button>
                            @if($contact->is_active)
                            <button class="btn btn-sm btn-info merge-contacts" data-id="{{ $contact->id }}">Merge</button>
                            @endif
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <!-- Pagination Links -->
        <div class="pagination">
            {{ $contacts->links() }}
        </div>
    </div>
</div>

<style>
    .merged-contact {
        font-size: 0.9em;
        color: #666;
        margin-top: 5px;
        padding-top: 5px;
        border-top: 1px dashed #ddd;
    }
</style>