<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = ['name', 'type', 'is_required', 'show_on_table'];

    public function values() {
        return $this->hasMany(ContactCustomFieldValue::class);
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_custom_field_values')
                    ->withPivot('value')
                    ->withTimestamps();
    }
}
