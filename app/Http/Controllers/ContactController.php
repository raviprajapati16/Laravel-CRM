<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactCustomFieldValue;
use App\Models\ContactMerge;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query()->with(['customFields' => function($query) {
            $query->where('show_on_table', true);
        }]);
        
        if ($request->has('name') && $request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('email') && $request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->has('gender') && $request->gender) {
            $query->where('gender', $request->gender);
        }

        $contacts = $query->paginate(10);;
        $customFields = CustomField::where('show_on_table', true)->get();
        
        if ($request->ajax()) {
            return view('contacts.partials.contacts_table', compact('contacts', 'customFields'))->render();
        }

        return view('contacts.index', compact('contacts', 'customFields'));
    }
    
    public function contactsfetch(Request $request)
    {
        try {
            // Get all active contacts (excluding the one being merged if provided)
            $contacts = Contact::query()
            ->when($request->exclude, function($query, $excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->orderBy('name')
            ->get();
            
            // Format the response
            $formattedContacts = $contacts->map(function($contact) {
                return [
                    'id' => $contact->id,
                    'text' => $contact->name . ' (' . $contact->email . ')'
                ];
            });
            
            return response()->json([
                'status' => 'success',
                'contacts' => $formattedContacts
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch contacts: ' . $e->getMessage(),
                'contacts' => []
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            // Eager load contact with custom fields and merge history
            $contact = Contact::with([
                'customFields' => function($query) {
                    $query->select('custom_fields.id', 'name', 'type', 'is_required')
                        ->withPivot('value');
                },
                'mergesAsMaster',
                'mergesAsMerged'
            ])->findOrFail($id);

            // Format the response data
            $formattedContact = [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'gender' => $contact->gender,
                'profile_image_url' => $contact->profile_image ? asset('storage/'.$contact->profile_image) : null,
                'additional_file_url' => $contact->additional_file ? asset('storage/'.$contact->additional_file) : null,
                'is_active' => $contact->is_active,
                'custom_fields' => $contact->customFields->map(function($field) {
                    return [
                        'id' => $field->id,
                        'field_name' => $field->field_name,
                        'field_type' => $field->field_type,
                        'is_required' => $field->is_required,
                        'value' => $field->pivot->value
                    ];
                }),
                'merge_history' => [
                    'as_master' => $contact->mergesAsMaster,
                    'as_merged' => $contact->mergesAsMerged
                ]
            ];

            return response()->json([
                'status' => 'success',
                'data' => $formattedContact
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error("Contact API Error - Show: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve contact data',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Base validation rules
            $rules = [
                'name' => 'required',
                'email' => ['required', 'email', Rule::unique('contacts', 'email')->ignore($request->id)],
                'phone' => 'required',
                'gender' => 'required',
                'profile_image' => 'nullable|image',
                'additional_file' => 'nullable|file',
            ];
            
            $messages = [];
            // Dynamically validate required custom fields
            $requiredFields = CustomField::where('is_required', true)->get();
            foreach ($requiredFields as $field) {
                $fieldKey = "custom_fields.{$field->id}";
                $rules[$fieldKey] = 'required';
                $messages["{$fieldKey}.required"] = "The {$field->name} field is required.";
            }

            $request->validate($rules, $messages);

            DB::beginTransaction();
            
            // Create or update contact
            $contact = $request->id ? Contact::find($request->id) : new Contact;
            $contact->name = $request->name;
            $contact->email = $request->email;
            $contact->phone = $request->phone;
            $contact->gender = $request->gender;

            if ($request->hasFile('profile_image')) {
                if ($contact->profile_image) {
                    Storage::disk('public')->delete($contact->profile_image);
                }
                $contact->profile_image = $request->file('profile_image')->store('uploads', 'public');
            }

            if ($request->hasFile('additional_file')) {
                if ($contact->additional_file) {
                    Storage::disk('public')->delete($contact->additional_file);
                }
                $contact->additional_file = $request->file('additional_file')->store('uploads', 'public');
            }

            $contact->save();

            // Save custom field values
            if ($request->has('custom_fields')) {
                $customFieldsData = [];
                
                foreach ($request->custom_fields as $fieldId => $value) {
                    // Only process if the field exists and value is not empty
                    if (CustomField::find($fieldId) && !empty($value)) {
                        $customFieldsData[$fieldId] = ['value' => $value];
                    }
                }
                
                // Sync custom fields (will detach any not included in the array)
                $contact->customFields()->sync($customFieldsData);
            } else {
                // If no custom fields are submitted, detach all existing ones
                $contact->customFields()->detach();
            }
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $request->id ? 'Contact updated successfully.' : 'Contact added successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            throw $e; // Let Laravel handle validation error response
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Contact Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            // Delete files
            if ($contact->profile_image) Storage::disk('public')->delete($contact->profile_image);
            if ($contact->additional_file) Storage::disk('public')->delete($contact->additional_file);

            $contact->delete();
            return response()->json(['status' => 'deleted', 'message' => 'Contact deleted.']);
        } catch (\Exception $e) {
            Log::error('Delete Contact Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete contact.'], 500);
        }
    }

    public function merge(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'master_contact_id' => 'required|exists:contacts,id',
                'merged_contact_id' => 'required|exists:contacts,id|different:master_contact_id',
            ]);

            // Load contacts with their custom field values
            $master = Contact::with('customFields')->findOrFail($request->master_contact_id);
            $merged = Contact::with('customFields')->findOrFail($request->merged_contact_id);

            $mergeDetails = [
                'merged_at' => now()->toDateTimeString(),
                'field_changes' => [],
                'custom_field_changes' => []
            ];

            // Merge standard contact fields
            $this->mergeStandardFields($master, $merged, $mergeDetails);

            // Handle email separately
            $this->mergeEmailField($master, $merged, $mergeDetails);

            // Merge custom fields
            $this->mergeCustomFields($master, $merged, $mergeDetails);

            // Create merge record
            $mergeRecord = ContactMerge::create([
                'master_contact_id' => $master->id,
                'merged_contact_id' => $merged->id,
                'merge_details' => json_encode($mergeDetails),
            ]);

            $merged->is_active = false;
            $merged->merged_into_id = $master->id;
            $merged->merge_record_id = $mergeRecord->id;
            
            if (!$merged->save()) {
                throw new \Exception('Failed to update merged contact status');
            }
            
            // Verify the update
            $merged->refresh();
            if ($merged->is_active || $merged->merged_into_id !== $master->id) {
                throw new \Exception('Contact merge status not persisted correctly');
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Contacts merged successfully',
                'data' => [
                    'master_contact' => $master->load('customFields'),
                    'merge_record' => $mergeRecord
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Merge Failed: '.$e->getMessage()."\n".$e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Merge failed: '.$e->getMessage()
            ], 500);
        }
    }

    protected function mergeStandardFields($master, $merged, &$mergeDetails)
    {
        $standardFields = ['phone', 'gender', 'profile_image', 'additional_file'];
        
        foreach ($standardFields as $field) {
            if (empty($master->$field) && !empty($merged->$field)) {
                $mergeDetails['field_changes'][$field] = [
                    'action' => 'copied_from_merged',
                    'old_value' => $master->$field,
                    'new_value' => $merged->$field
                ];
                $master->$field = $merged->$field;
            }
        }
    }

    protected function mergeEmailField($master, $merged, &$mergeDetails)
    {
        if ($master->email !== $merged->email) {
            $mergeDetails['additional_emails'] = $merged->email;
            $mergeDetails['field_changes']['email'] = [
                'action' => 'additional_stored',
                'master_value' => $master->email,
                'merged_value' => $merged->email
            ];
        }
    }

    protected function mergeCustomFields($master, $merged, &$mergeDetails)
    {
        foreach ($merged->customFields as $mergedCustomField) {
            $existingField = $master->customFields->firstWhere('id', $mergedCustomField->id);
            
            if (!$existingField) {
                // Add new custom field from merged contact
                $master->customFields()->attach($mergedCustomField->id, [
                    'value' => $mergedCustomField->pivot->value
                ]);
                
                $mergeDetails['custom_field_changes'][] = [
                    'custom_field_id' => $mergedCustomField->id,
                    'name' => $mergedCustomField->name,
                    'action' => 'added_from_merged',
                    'value' => $mergedCustomField->pivot->value
                ];
            } elseif ($existingField->pivot->value !== $mergedCustomField->pivot->value) {
                // Handle conflict (keeping master value in this implementation)
                $mergeDetails['custom_field_changes'][] = [
                    'custom_field_id' => $mergedCustomField->id,
                    'name' => $mergedCustomField->name,
                    'action' => 'kept_master_value',
                    'master_value' => $existingField->pivot->value,
                    'merged_value' => $mergedCustomField->pivot->value
                ];
            }
        }
    }

    public function mergePreview(Request $request)
    {
        $master = Contact::with('customFields')->findOrFail($request->master_id);
        $merged = Contact::with('customFields')->findOrFail($request->merged_id);

        return view('contacts.partials.merge_preview', compact('master', 'merged'));
    }
}
