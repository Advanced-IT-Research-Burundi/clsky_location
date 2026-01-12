<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where(function ($q) {
            $q->where('receiver_id', auth()->id())
            ->orWhere('sender_id', auth()->id());
        })
            ->where('is_archived', false) // 
            ->with(['sender', 'receiver'])
            ->latest()
            ->paginate(20);

        $unreadCount = Message::where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->count();
        return view('messages.index', compact('messages', 'unreadCount'));
    }

    public function create()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('messages.create', compact('users', ));
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return redirect()->route('messages.index')
            ->with('success', 'Message supprimé avec succès');
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'property_id' => 'nullable|exists:properties,id',
            'attachments.*' => 'nullable|file|mimes:jpeg,jpg,png,pdf,doc,docx|max:10240'
        ]);

        // Créer le message
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'content' => $request->content,
            'property_id' => $request->property_id
        ]);

        // Gestion des pièces jointes
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message-attachments', 'public');

                $message->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize()
                ]);
            }
        }

        return redirect()->route('messages.show', $message)
            ->with('success', 'Message envoyé avec succès');
    }


    // public function archive(Message $message)
    // {
    //     $message->update([
    //         'is_archived' => true,
    //         'archived_at' => now()
    //     ]);

    //     return back()->with('success', 'Message archivé avec succès');
    // }

    // public function unarchive(Message $message)
    // {
    //     $message->update([
    //         'is_archived' => false,
    //         'archived_at' => null
    //     ]);

    //     return back()->with('success', 'Message désarchivé avec succès');
    // }

    public function downloadAttachment($id)
    {
        $attachment = MessageAttachment::findOrFail($id);
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            return back()->with('error', 'Fichier non trouvé');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->file_name
        );
    }

    public function show(Message $message)
    {
        // Vérifier si l'utilisateur a le droit de voir ce message
        if (!$this->canViewMessage($message)) {
            return redirect()->route('messages.index')
                ->with('error', 'Vous n\'avez pas accès à ce message.');
        }

        // Marquer comme lu si c'est le destinataire
        if ($message->receiver_id === auth()->id() && !$message->read_at) {
            $message->update(['read_at' => now()]);
        }

        // Récupérer les messages liés (même sujet ou même propriété)
        $relatedMessages = Message::where(function ($query) use ($message) {
            $query->where('subject', 'like', 'Re: ' . $message->subject)
                ->orWhere('subject', $message->subject);

            if ($message->property_id) {
                $query->orWhere('property_id', $message->property_id);
            }
        })
            ->where('id', '!=', $message->id)
            ->where(function ($query) {
                $query->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->take(5)
            ->get();

        return view('messages.show', compact('message', 'relatedMessages'));
    }

    private function canViewMessage(Message $message)
    {
        return auth()->id() === $message->sender_id ||
            auth()->id() === $message->receiver_id;
    }

    public function archived()
    {
        $messages = Message::where(function ($query) {
            $query->where('sender_id', auth()->id())
                ->orWhere('receiver_id', auth()->id());
        })
            ->where('is_archived', true)
            ->with(['sender', 'receiver', 'property'])
            ->latest()
            ->paginate(15);

        return view('messages.archived', compact('messages'));
    }

    public function archive(Message $message)
    {
        // Vérifier si l'utilisateur a le droit d'archiver ce message
        if ($message->sender_id !== auth()->id() && $message->receiver_id !== auth()->id()) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à archiver ce message.');
        }

        $message->update([
            'is_archived' => true,
            'archived_at' => now()
        ]);

        return back()->with('success', 'Message archivé avec succès.');
    }

    public function unarchive(Message $message)
    {
        // Vérifier si l'utilisateur a le droit de désarchiver ce message
        if ($message->sender_id !== auth()->id() && $message->receiver_id !== auth()->id()) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à désarchiver ce message.');
        }

        $message->update([
            'is_archived' => false,
            'archived_at' => null
        ]);

        return back()->with('success', 'Message désarchivé avec succès.');
    }
}
