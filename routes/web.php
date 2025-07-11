<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomFieldController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
Route::get('/contacts/{id}', [ContactController::class, 'edit'])->name('contacts.edit');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
Route::get('/contactsfetch', [ContactController::class, 'contactsfetch'])->name('contactsfetch');
Route::post('/contacts/merge', [ContactController::class, 'merge'])->name('contacts.merge');
Route::get('/contacts/merge/preview', [ContactController::class, 'mergePreview'])->name('contacts.merge.preview');



// Admin Custom Fields
Route::get('/custom-fields', [CustomFieldController::class, 'index'])->name('custom-fields.index');
Route::get('/custom-fields/fetch', [CustomFieldController::class, 'fetch'])->name('custom-fields.fetch');
Route::post('/custom-fields', [CustomFieldController::class, 'store'])->name('custom-fields.store');
Route::delete('/custom-fields/{id}', [CustomFieldController::class, 'destroy'])->name('custom-fields.destroy');
