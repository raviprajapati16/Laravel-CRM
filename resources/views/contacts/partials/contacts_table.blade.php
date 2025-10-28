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
                    @if(!$contact->merged_into_id)
                    <tr class="master-contact">
                        {{-- Name Column --}}
                        <td>
                            <div class="contact-hierarchy">
                                @php
                                    function displayContactHierarchy($contact, $level = 0) {
                                        $html = '';
                                        $indent = $level * 15;
                                        $borderColor = $level == 0 ? '#007bff' : ($level == 1 ? '#ffc107' : ($level == 2 ? '#17a2b8' : '#6c757d'));
                                        
                                        $html .= '<div class="hierarchy-item level-' . $level . '" style="padding-left: ' . $indent . 'px; ' . ($level > 0 ? 'border-left: 2px solid ' . $borderColor . ';' : '') . '">';
                                        
                                        if($level == 0) {
                                            if($contact->profile_image) {
                                                $html .= '<img src="' . asset('storage/' . $contact->profile_image) . '" width="25" class="img-thumbnail mr-2">';
                                            }
                                            $html .= '<strong>' . $contact->name . '</strong>';
                                            $html .= '<span class="badge bg-primary text-white merge-badge ms-1">Primary</span>';
                                        } else {
                                            $html .= '<div class="d-flex align-items-center">';
                                            if($contact->profile_image) {
                                                $html .= '<img src="' . asset('storage/' . $contact->profile_image) . '" width="25" class="img-thumbnail mr-2">';
                                            }
                                            $html .= '<div>' . $contact->name . '</div>';
                                            $html .= '<span class="badge bg-warning text-dark merge-badge ms-2">Merged</span>';
                                            if($level > 1) {
                                                $html .= '<span class="badge bg-info text-dark merge-badge ms-1">Level ' . $level . '</span>';
                                            }
                                            $html .= '</div>';
                                        }
                                        
                                        $html .= '</div>';
                                        
                                        // Recursively display merged contacts
                                        if($contact->mergedContacts->count() > 0) {
                                            foreach($contact->mergedContacts as $merged) {
                                                $html .= displayContactHierarchy($merged, $level + 1);
                                            }
                                        }
                                        
                                        return $html;
                                    }
                                    
                                    function displayEmailHierarchy($contact, $level = 0) {
                                        $html = '';
                                        $indent = $level * 15;
                                        
                                        if($level == 0) {
                                            $html .= '<div><strong>' . $contact->email . '</strong></div>';
                                        } else {
                                            $html .= '<div class="hierarchy-item level-' . $level . '" style="padding-left: ' . $indent . 'px">';
                                            $html .= '<div class="text-muted small">' . $contact->email . '</div>';
                                            $html .= '</div>';
                                        }
                                        
                                        // Recursively display merged emails
                                        if($contact->mergedContacts->count() > 0) {
                                            foreach($contact->mergedContacts as $merged) {
                                                $html .= displayEmailHierarchy($merged, $level + 1);
                                            }
                                        }
                                        
                                        return $html;
                                    }
                                    
                                    function displayPhoneHierarchy($contact, $level = 0) {
                                        $html = '';
                                        $indent = $level * 15;
                                        
                                        if($level == 0) {
                                            $html .= '<div><strong>' . $contact->phone . '</strong></div>';
                                        } else {
                                            $html .= '<div class="hierarchy-item level-' . $level . '" style="padding-left: ' . $indent . 'px">';
                                            $html .= '<div class="text-muted small">' . $contact->phone . '</div>';
                                            $html .= '</div>';
                                        }
                                        
                                        // Recursively display merged phones
                                        if($contact->mergedContacts->count() > 0) {
                                            foreach($contact->mergedContacts as $merged) {
                                                $html .= displayPhoneHierarchy($merged, $level + 1);
                                            }
                                        }
                                        
                                        return $html;
                                    }
                                    
                                    function displayGenderHierarchy($contact, $level = 0) {
                                        $html = '';
                                        
                                        if($level == 0) {
                                            $html .= '<span class="badge bg-primary">' . ucfirst($contact->gender) . '</span>';
                                        } else {
                                            $html .= '<div class="hierarchy-item">';
                                            $html .= '<span class="badge bg-secondary">' . ucfirst($contact->gender) . '</span>';
                                            $html .= '</div>';
                                        }
                                        
                                        // Recursively display merged genders
                                        if($contact->mergedContacts->count() > 0) {
                                            foreach($contact->mergedContacts as $merged) {
                                                $html .= displayGenderHierarchy($merged, $level + 1);
                                            }
                                        }
                                        
                                        return $html;
                                    }
                                    
                                    function displayCustomFieldHierarchy($contact, $fieldId, $level = 0) {
                                        $value = $contact->customFields->firstWhere('id', $fieldId)?->pivot->value ?? '';
                                        $html = '';
                                        
                                        if($level == 0) {
                                            $html .= '<div><strong>' . $value . '</strong></div>';
                                        } else {
                                            $html .= '<div class="hierarchy-item">';
                                            $html .= '<div class="text-muted small">' . $value . '</div>';
                                            $html .= '</div>';
                                        }
                                        
                                        // Recursively display merged custom fields
                                        if($contact->mergedContacts->count() > 0) {
                                            foreach($contact->mergedContacts as $merged) {
                                                $html .= displayCustomFieldHierarchy($merged, $fieldId, $level + 1);
                                            }
                                        }
                                        
                                        return $html;
                                    }
                                @endphp
                                
                                {!! displayContactHierarchy($contact) !!}
                            </div>
                        </td>
                        
                        {{-- Email Column --}}
                        <td>
                            <div class="email-hierarchy">
                                {!! displayEmailHierarchy($contact) !!}
                            </div>
                        </td>
                        
                        {{-- Phone Column --}}
                        <td>
                            <div class="phone-hierarchy">
                                {!! displayPhoneHierarchy($contact) !!}
                            </div>
                        </td>
                        
                        {{-- Gender Column --}}
                        <td>
                            <div class="gender-hierarchy">
                                {!! displayGenderHierarchy($contact) !!}
                            </div>
                        </td>
                        
                        {{-- Custom Fields Columns --}}
                        @foreach($customFields as $field)
                            @if ($field->show_on_table)
                            <td>
                                <div class="custom-field-hierarchy">
                                    {!! displayCustomFieldHierarchy($contact, $field->id) !!}
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
    
    .level-0 { font-weight: bold; }
    .level-1 { border-left: 2px solid #ffc107; margin-left: 5px; }
    .level-2 { border-left: 2px solid #17a2b8; margin-left: 10px; }
    .level-3 { border-left: 2px solid #6c757d; margin-left: 15px; }
    
    .merge-badge {
        font-size: 0.7em;
        padding: 1px 4px;
    }
</style>