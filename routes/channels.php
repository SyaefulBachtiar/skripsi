<?php

use App\Models\ChatRooms;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('servisio-chat.{roomId}', function ($user, $roomId) {
    // Cari room-nya
    $room = ChatRooms::find($roomId);

    if (!$room) {
        return false;
    }

    // Pastikan user_id yang login sama dengan user_id di customer atau technician
    // Note: Sesuaikan 'user_id' dengan nama kolom di tabel customer/technician kamu
    $isCustomer = $room->customer && $room->customer->user_id == $user->id;
    $isTechnician = $room->technician && $room->technician->user_id == $user->id;

    return $isCustomer || $isTechnician;
});
