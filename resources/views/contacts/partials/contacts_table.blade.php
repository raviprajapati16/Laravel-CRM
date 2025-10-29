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
                @if($contacts->count() > 0)
                    @foreach($contacts as $contact)
                        @if(!$contact->merged_into_id)
                        <tr class="master-contact">
                            {{-- Name Column --}}
                            <td>
                                <div class="contact-hierarchy">
                                    @include('contacts.partials.contact_hierarchy', [
                                        'contact' => $contact, 
                                        'level' => 0
                                    ])
                                </div>
                            </td>
                            
                            {{-- Email Column --}}
                            <td>
                                <div class="email-hierarchy">
                                    @include('contacts.partials.email_hierarchy', [
                                        'contact' => $contact, 
                                        'level' => 0
                                    ])
                                </div>
                            </td>
                            
                            {{-- Phone Column --}}
                            <td>
                                <div class="phone-hierarchy">
                                    @include('contacts.partials.phone_hierarchy', [
                                        'contact' => $contact, 
                                        'level' => 0
                                    ])
                                </div>
                            </td>
                            
                            {{-- Gender Column --}}
                            <td>
                                <div class="gender-hierarchy">
                                    @include('contacts.partials.gender_hierarchy', [
                                        'contact' => $contact, 
                                        'level' => 0
                                    ])
                                </div>
                            </td>
                            
                            {{-- Custom Fields Columns --}}
                            @foreach($customFields as $field)
                                @if ($field->show_on_table)
                                <td>
                                    <div class="custom-field-hierarchy">
                                        @include('contacts.partials.custom_field_hierarchy', [
                                            'contact' => $contact, 
                                            'fieldId' => $field->id,
                                            'level' => 0
                                        ])
                                    </div>
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
                @else
                    <tr>
                        <td colspan="{{ 4 + $customFields->where('show_on_table', true)->count() + 1 }}" class="text-center py-3">
                            <div class="empty-state">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Contacts Found</h5>
                                <p class="text-muted mb-4">There are no contacts to display at the moment.</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="pagination">
            {{ $contacts->links() }}
        </div>
    </div>
</div>

<style>
    .master-contact {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
    }
    
    .hierarchy-item {
        margin: 3px 0;
        padding: 2px 0;
    }
    
    .level-0 { padding-left: 0px; }
    .level-1 { padding-left: 15px; border-left: 2px solid #ffc107; }
    .level-2 { padding-left: 30px; border-left: 2px solid #17a2b8; }
    .level-3 { padding-left: 45px; border-left: 2px solid #6c757d; }
    
    .merge-badge {
        font-size: 0.7em;
        padding: 1px 4px;
    }
</style>