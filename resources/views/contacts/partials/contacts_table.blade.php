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
                        <tr>
                            <td>
                                @if($contact->profile_image)
                                <img src="{{ asset('storage/' . $contact->profile_image) }}" alt="Profile Image" width="50" class="img-thumbnail mr-2">
                                @endif
                                {{ $contact->name }}
                                @if(!$contact->is_active)
                                <span class="badge badge-primary text-black">Merged</span>
                                @endif
                            </td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->phone }}</td>
                            <td>{{ ucfirst($contact->gender) }}</td>
                            @foreach($customFields as $field)
                                <td>
                                   {{ $contact->customFields->firstWhere('id', $field->id)?->pivot->value ?? '' }}
                                </td>
                            @endforeach
                            <td>
                                <button class="btn btn-sm btn-primary edit-contact" data-id="{{ $contact->id }}">Edit</button>
                                <button class="btn btn-sm btn-danger deleteForm" data-id="{{ $contact->id }}">Delete</button>
                                @if($contact->is_active)
                                <button class="btn btn-sm btn-info merge-contacts" data-id="{{ $contact->id }}">Merge</button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                </tbody>
            </table>
            <!-- Pagination Links -->
            <div class="pagination">
                {{ $contacts->links() }}
            </div>
        </div>
    </div>