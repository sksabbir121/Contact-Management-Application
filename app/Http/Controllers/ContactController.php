<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');
    $contacts = Contact::when($search, function ($query, $search) {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
    });

    $sort = $request->input('sort', 'name');
    $direction = $request->input('direction', 'asc');
    $contacts = $contacts->orderBy($sort, $direction)->paginate(10);

    return view('contacts.index', compact('contacts', 'search', 'sort', 'direction'));
}

public function create()
{
    return view('contacts.create');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:contacts',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
    ]);

    Contact::create($validated);

    return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
}

public function edit($id)
{
    $contact = Contact::findOrFail($id);
    return view('contacts.edit', compact('contact'));
}

public function update(Request $request, $id)
{
    $contact = Contact::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:contacts,email,' . $contact->id,
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
    ]);

    $contact->update($validated);

    return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
}

public function destroy($id)
{
    $contact = Contact::findOrFail($id);
    $contact->delete();

    return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
}


}
