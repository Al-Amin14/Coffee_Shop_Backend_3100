<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
   
    public function store(Request $request)
    {
        
        Log::info('Store Comment Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $comment = new Comment();
            $comment->user_id = Auth::id(); 
            $comment->product_id = $request->product_id;
            $comment->comment = $request->comment;
            $comment->save();

            return response()->json([
                'message' => 'Comment posted successfully',
                'comment' => $comment
            ], 201);
        } catch (\Exception $e) {
            
            Log::error('Error while saving comment: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to post comment'], 500);
        }
    }

    
    public function index($productId)
    {
        try {
            $comments = Comment::with('user')->where('product_id', $productId)->get();
            return response()->json([
                'comments' => $comments
            ]);
        } catch (\Exception $e) {
            Log::error('Error while fetching comments: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to load comments'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $comment = Comment::find($id);

            if (!$comment || $comment->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized or comment not found'], 403);
            }

            $comment->delete();

            return response()->json([
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error while deleting comment: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete comment'], 500);
        }
    }
}
