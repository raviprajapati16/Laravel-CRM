<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
