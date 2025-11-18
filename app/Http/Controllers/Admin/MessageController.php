<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Messages';
    }

    protected function getMessages()
    {
        return ContactMessage::searchable(['full_name', 'email', 'phone', 'subject'], false)
            ->orderBy('id', 'desc');
    }

    public function index()
    {
        $pageTitle = $this->pageTitle;
        $messages = $this->getMessages()->paginate(getPaginate());
        return view('admin.messages.list', compact('pageTitle', 'messages'));
    }

    public function view($id)
    {
        $pageTitle = "Message Details";
        $message = ContactMessage::findOrFail($id);
        return view('admin.messages.view', compact('pageTitle', 'message'));
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
        
        $notify[] = ['success', 'Message deleted successfully'];
        return back()->withNotify($notify);
    }
}
