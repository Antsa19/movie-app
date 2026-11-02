<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reply;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'movie', 'reply.admin'])->latest()->paginate(10);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted.');
    }

    public function storeReply(Request $request, Review $review)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        Reply::updateOrCreate(
            ['review_id' => $review->id],
            ['user_id' => $request->user()->id, 'message' => $data['message']]
        );

        return back()->with('success', 'Reply saved.');
    }

    public function destroyReply(Reply $reply)
    {
        $reply->delete();

        return back()->with('success', 'Reply deleted.');
    }
}