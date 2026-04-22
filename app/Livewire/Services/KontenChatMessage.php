<?php

namespace App\Livewire\Services;

use App\Models\ChatMessages;
use App\Models\ChatRooms;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class KontenChatMessage extends Component
{

    use WithFileUploads;

    public $roomChatId;
    public $message = '';
    public $data_pesanan = '';

    public $photo;
    public $photoPreview;

    protected $listeners = ['refreshMessages' => '$refresh'];

    public function mount () {
        $this->data_pesanan = ChatRooms::where('id', $this->roomChatId)
            ->select('id', 'order_id')
            ->with([
                    'order:id,id_jasa,order_date,order_time,total_harga',
                    'order.jasa:id,nama_jasa,thumbnails',
                    'order.lacak_pesanan' => function ($q) {
                        $q->select('id','id_order', 'status_order')
                            ->latest();
                    }
                ])
            ->first();

        if (!$this->data_pesanan) {
            abort(404, 'Percakapan tidak ditemukan');
        }

            // dd($this->data_pesanan->toArray());
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:5120',
        ]);

        // Buat preview
        $this->photoPreview = $this->photo->temporaryUrl();
    }

    public function removePhoto()
    {
        $this->reset(['photo', 'photoPreview']);
    }

    public function getMessagesProperty()
    {
        return ChatMessages::where('chat_room_id', $this->roomChatId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendPhotoMessage()
    {
        $this->validate([
            'photo' => 'required|image|max:5120',
        ]);

        try {
            $room = ChatRooms::where('id', $this->roomChatId)
                ->where(function($query) {
                    $query->whereHas('customer', function($q) {
                        $q->where('user_id', Auth::id());
                    })
                    ->orWhereHas('technician', function($q) {
                        $q->where('user_id', Auth::id());
                    });
                })
                ->firstOrFail();

            // Simpan foto ke storage
            $photoPath = $this->photo->store('chat-photos', 'public');

            ChatMessages::create([
                'chat_room_id' => $room->id,
                'sender_id'    => Auth::id(),
                'message'      => $photoPath,
                'type'         => 'image',
                'is_read'      => false
            ]);

            $room->touch();
            $this->reset(['photo', 'photoPreview', 'message']);
            $this->dispatch('scroll-to-bottom');

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim foto: ' . $e->getMessage());
        }
    }

    public function sendMessage()
    {
        if (!$this->photo && empty(trim($this->message))) {
            return;
        }

        $validatedData = $this->validate([
            'message' => 'required|string|max:1000',
        ]);


        try {
            $room = ChatRooms::where('id', $this->roomChatId)
                ->where(function($query) {
                    $query->whereHas('customer', function($q) {
                        $q->where('user_id', Auth::id());
                    })
                    ->orWhereHas('technician', function($q) {
                        $q->where('user_id', Auth::id());
                    });
                })
                ->firstOrFail();

            if ($this->photo) {
                $this->validate(['photo' => 'image|max:5120']);
                
                $photoPath = $this->photo->store('chat-photos', 'public');

                ChatMessages::create([
                    'chat_room_id' => $room->id,
                    'sender_id'    => Auth::id(),
                    'message'      => $photoPath,
                    'type'         => 'image',
                    'is_read'      => false
                ]);
            }

            if (!empty(trim($this->message))) {
                $this->validate(['message' => 'required|string|max:1000']);

                ChatMessages::create([
                    'chat_room_id' => $room->id,
                    'sender_id'    => Auth::id(),
                    'message'      => strip_tags($this->message),
                    'type'         => 'text', // Pastikan type default adalah text
                    'is_read'      => false
                ]);
            }

            $room->touch();
            $this->reset(['photo', 'photoPreview', 'message']);
            $this->dispatch('scroll-to-bottom');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Jika user mencoba kirim pesan ke room yang bukan miliknya
            session()->flash('error', 'Akses ditolak atau percakapan tidak ditemukan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim pesan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.services.konten-chat-message', [
            'messages' => $this->messages
        ]);
    }
}
