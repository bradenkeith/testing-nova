<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FileDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $file_path;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(\App\File $file)
    {
        $this->file_path = $file->file_path;

        Storage::delete($this->file_path);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
