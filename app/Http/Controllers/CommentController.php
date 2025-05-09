<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'document_id' => 'required',
                'content' => 'required|string'
            ]);

            $comment = Comment::create([
                'document_id' => $validated['document_id'],
                'user_name' => 'Admin', // Or use Auth::user()->name
                'content' => $validated['content']
            ]);

            return response()->json($comment);
        } catch (\Exception $e) {
            Log::error('Comment creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create comment'], 500);
        }
    }

    public function getComments($documentId)
    {
        try {
            $comments = Comment::where('document_id', $documentId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($comments);
        } catch (\Exception $e) {
            Log::error('Failed to fetch comments: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch comments'], 500);
        }
    }
}