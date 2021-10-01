<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Scaners;
use Ratchet\ConnectionInterface;

class RegisterScanerEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    private $connection;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Scaners $scaner)
    {
        //
    }

    public function onOpen(ConnectionInterface $connection)
    {
        // TODO: Implement onOpen() method.
        $this->connection = $connection;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('channel-scaner');
    }
}
