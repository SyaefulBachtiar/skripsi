<?php

namespace App\Livewire\Services\PesanTechnician;

use App\Events\PesananMasuk;
use App\Models\ChatMessages;
use App\Models\ChatRooms;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class KontenPesan extends Component
{
    use WithPagination;

    public $search = '';

    protected function getListeners()
    {
        return [
            'refreshMessages' => '$refresh'
        ];
    }

    public function navigateChatMsg ($id) {
        try {
            ChatMessages::where('chat_room_id', $id)
                ->where('sender_id', '!=', Auth::id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true
                ]);
            
            return $this->redirect(route('chat.room', ['id' => $id]), navigate: true);
        } catch (Exception $e) {
            session()->flash('error', 'gagal', $e);
            return $this->redirect(request()->header('Referer'), navigate: true);
        }
    }

    public function updatingSearch () {
        $this->resetPage();
    }

    public function render()
    {

        $data = ChatRooms::where('technician_id', Auth::user()->technician->id)
            ->select('id', 'customer_id', 'order_id')
            ->with([
                    'last_message',
                    'order:id,id_jasa',
                    'order.jasa:id,nama_jasa',
                    'customer:id,user_id',
                    'customer.user:id,name'
                    ])
            ->withCount(['chat_message as unread_messages_count' => function ($query) {
                $query->where('is_read', false)
                    ->where('sender_id', '!=', Auth::id()); // Hitung pesan masuk saja
            }])
            ->when($this->search, function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->whereHas('user', function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->orderBy(
                ChatMessages::select('created_at')
                    ->whereColumn('chat_room_id', 'chat_rooms.id')
                    ->latest()
                    ->limit(1), 
                'desc'
            )
            ->paginate(10);

            // dd($data->toArray());

        return view('livewire.services.pesan-technician.konten-pesan', [
            'data_pesan' => $data
        ]);
    }
}
