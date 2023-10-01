<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->type === 'admin') {
            $updates = Contact::paginate(10);
            return response()->json($updates);
        } else {
            return response()->json(['message' => "You don't have permission to get the data"], 403); // Use 403 for forbidden access
        }
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        if ($user->type === 'admin') {
            $data = Contact::where('id', $request->id)->first();
            if ($data) {
                return response()->json($data);
            } else {
                return response()->json(['message' => 'Contact not found'], 404);
            }
        } else {
            return response()->json(['message' => "You don't have permission to get the data"], 403); // Use 403 for forbidden access
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email',
            'phone' => 'required|string|max:15',
            'message' => 'required|string'
        ]);

        $validatedData['firstname'] = ucwords($validatedData['firstname']);
        $validatedData['lastname'] = ucwords($validatedData['lastname']);
        // Format the phone number to Philippine format
        $formattedPhone = '0' . substr(preg_replace('/[^0-9]/', '', $validatedData['phone']), -10);
        $validatedData['phone'] = $formattedPhone;

        // Create the contact and store it in a variable
        $contact = Contact::create($validatedData);

        return response()->json(['message' => 'Contact created successfully', 'contact' => $contact]);
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();
        if ($user->type === 'admin') {
            $contact = Contact::find($request->id);
            if ($contact) {
                $contact->delete();
                return response()->json(['message' => 'Contact deleted successfully']);
            } else {
                return response()->json(['message' => 'Contact not found'], 404);
            }
        } else {
            return response()->json(['message' => "You don't have permission to delete the data"], 403); // Use 403 for forbidden access
        }
    }
}
