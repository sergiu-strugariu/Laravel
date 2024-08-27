<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $messagesQuery = Message::getMessagesQueryBetweenTwoUsers(auth()->user()->id, $request->receiver_id);
        $result = $messagesQuery->orderBy('created_at', "DESC")->limit(30)->get();

        return response()->json(data: ['status' => true, 'messages' => $result]);
    }

    public function store(Request $request)
    {
        if (!$request->content) {
            return response()->json(data: ['status' => false], status: 500);
        }

        $message = Message::create([
            "sender_id" =>  auth()->user()->id,
            "receiver_id" => $request->receiver_id,
            "content" =>$request->content
        ]);

        MessageSent::dispatch($message);

        return response()->json(data: ['status' => true, 'message' =>  $message], status: 201);
    }
}
