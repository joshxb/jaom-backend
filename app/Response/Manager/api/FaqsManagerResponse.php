<?php

namespace App\Response\Manager\api;

use Illuminate\Http\Request;
use App\Models\faqs;
use Illuminate\Support\Facades\Auth;

class FaqsManagerResponse
{
    public function index()
    {
        $faqs = faqs::all();
        return response()->json($faqs);
    }

    public function create()
    {
        return response()->json(['message' => 'Create method']);
    }

    public function store(Request $request)
    {
        if (Auth::user()->type != 'admin') {
            return response()->json(['message' => 'Permission denied'], 401);
        }

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
        return response()->json(['message' => 'Edit method']);
    }

    public function update(Request $request, faqs $faq)
    {
        if (Auth::user()->type != 'admin') {
            return response()->json(['message' => 'Permission denied'], 401);
        }

        if ($request->filled('title')) {
            $faq->title = $request->input('title');
        }
        if ($request->filled('definition')) {
            $faq->definition = $request->input('definition');
        }

        $faq->save();
        return response()->json(['message' => 'Faq updated successfully']);
    }

    public function destroy(faqs $faq)
    {
        if (Auth::user()->type != 'admin') {
            return response()->json(['message' => 'Permission denied'], 401);
        }

        $faq->delete();
        return response()->json(['message' => 'Faq deleted successfully']);
    }
}
