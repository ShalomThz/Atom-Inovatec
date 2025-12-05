<?php

namespace App\Livewire;

use App\Models\Notificacion;
use Livewire\Component;
use Livewire\Attributes\On;

class NotificationBell extends Component
{
    public $notificaciones = [];
    public $noLeidas = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    #[On('notification-created')]
    public function loadNotifications()
    {
        $usuario = auth()->user();

        $this->notificaciones = Notificacion::where('user_id', $usuario->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        $this->noLeidas = Notificacion::where('user_id', $usuario->id)
            ->noLeidas()
            ->count();
    }

    public function marcarComoLeida($id)
    {
        $notificacion = Notificacion::where('user_id', auth()->id())
            ->find($id);

        if ($notificacion) {
            $notificacion->marcarComoLeida();
            $this->loadNotifications();
        }
    }

    public function marcarTodasComoLeidas()
    {
        Notificacion::where('user_id', auth()->id())
            ->noLeidas()
            ->update([
                'leida' => true,
                'leida_en' => now(),
            ]);

        $this->loadNotifications();
    }

    public function eliminar($id)
    {
        Notificacion::where('user_id', auth()->id())
            ->find($id)
            ?->delete();

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
