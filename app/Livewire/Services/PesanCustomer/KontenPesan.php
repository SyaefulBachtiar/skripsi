<?php

namespace App\Livewire\Services\PesanCustomer;

use App\Events\PesananMasuk;
use App\Models\ChatMessages;
use App\Models\ChatRooms;
use App\Models\Role_users\Customer;
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
            "echo-private:App.Models.User." . Auth::id() . ",.PesananMasuk" => '$refresh',
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

            broadcast(new PesananMasuk($id))->toOthers();
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
        $customerId = Customer::where('user_id', Auth::id())->value('id');

        $data_pesan = ChatRooms::where('customer_id', $customerId)
            ->select('id', 'technician_id', 'order_id')
            ->with([
                    'technician:id,nama_asli,foto_wajah', 
                    'last_message',
                    'order:id,id_jasa',
                    'order.jasa:id,nama_jasa',
                ])
            ->withCount(['chat_message as unread_messages_count' => function ($query) {
                $query->where('is_read', false)
                    ->where('sender_id', '!=', Auth::id()); // Hitung pesan masuk saja
            }])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('technician', function ($sub) {
                        $sub->where('nama_asli', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('order', function ($sub) {
                        $sub->whereHas('jasa', function ($inner) {
                            $inner->where('nama_jasa', 'like', '%' . $this->search . '%');
                        });
                    });
                });
            })
            ->latest('updated_at')
            ->paginate(10);

        // dd($data_pesan);

        return view('livewire.services.pesan-customer.konten-pesan', [
            'data_pesan' => $data_pesan
        ]);
    }
}
