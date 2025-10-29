<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Contact extends Model
{
    protected $table = 'contacts'; 
    
    protected $fillable = ['name', 'email', 'phone', 'gender', 'profile_image', 'additional_file', 'is_active', 'merged_into_id', 'merge_record_id'];

    public function customFields() {
       return $this->belongsToMany(CustomField::class, 'contact_custom_field_values')
                    ->withPivot('value')
                    ->withTimestamps();
    }

    public function mergesAsMaster()
    {
        return $this->hasMany(ContactMerge::class, 'master_contact_id');
    }

    public function mergesAsMerged()
    {
        return $this->hasMany(ContactMerge::class, 'merged_contact_id');
    }
    
    public function mergedInto()
    {
        return $this->belongsTo(Contact::class, 'merged_into_id');
    }

    public function mergedContacts()
    {
        return $this->hasMany(Contact::class, 'merged_into_id')
                ->with(['mergedContacts' => function($query) {
                    // Load nested merged contacts
                    $query->with(['mergedContacts' => function($q) {
                        $q->with('customFields');
                    }, 'customFields']);
                }, 'customFields'])
                ->with('customFields');
    }

    public function mergeRecord()
    {
        return $this->belongsTo(ContactMerge::class, 'merge_record_id');
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function($contact) {
            DB::transaction(function () use ($contact) {
                // 1. Handle contacts that reference this one as merged_into
                Contact::where('merged_into_id', $contact->id)
                    ->update(['merged_into_id' => null, 'is_active' => 1]);

                // 2. Handle merge_record_id references
                if ($contact->merge_record_id) {
                    // Delete the merge record first
                    ContactMerge::where('id', $contact->merge_record_id)->delete();
                }

                // 3. Delete merge records where this contact is involved
                ContactMerge::where('master_contact_id', $contact->id)
                    ->orWhere('merged_contact_id', $contact->id)
                    ->delete();

                // 4. Delete files
                if ($contact->profile_image) {
                    Storage::delete($contact->profile_image);
                }
                if ($contact->additional_file) {
                    Storage::delete($contact->additional_file);
                }
            });
        });
    }
}
