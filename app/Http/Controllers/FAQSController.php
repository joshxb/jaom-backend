<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\faqs;

class FAQSController extends Controller
{
    public function index()
    {
        $faqs = faqs::all();
        return response()->json($faqs);
    }

    public function create()
    {
        // Return JSON response if needed
        return response()->json(['message' => 'Create method']);
    }

    public function store(Request $request)
    {
        $faq = new faqs();
        $faq->title = $request->title;
        $faq->definition = $request->definition;
        $faq->save();

        return response()->json(['message' => 'Faq created successfully']);
    }

    public function show(faqs $faq)
    {
        return response()->json($faq);
    }

    public function edit(faqs $faq)
    {
        // Return JSON response if needed
        return response()->json(['message' => 'Edit method']);
    }

    public function update(Request $request, faqs $faq)
    {
        $faq->title = $request->input('title');
        $faq->definition = $request->input('definition');
        $faq->save();

        return response()->json(['message' => 'Faq updated successfully']);
    }

    public function destroy(faqs $faq)
    {
        $faq->delete();

        return response()->json(['message' => 'Faq deleted successfully']);
    }
}
