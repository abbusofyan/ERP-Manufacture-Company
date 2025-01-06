<?php

namespace App\Observers;

use App\Models\Storage;

class StorageObserver
{
    /**
     * Handle the Storage "created" event.
     *
     * @param \App\Models\Storage $storage
     * @return void
     */
    public function created(Storage $storage)
    {
        if (empty($storage->code)) {
            $storage->update(['code' => 'PD' . str_pad($storage->id, 5, '0', STR_PAD_LEFT)]);
        }
    }

    /**
     * Handle the Storage "updated" event.
     *
     * @param \App\Models\Storage $storage
     * @return void
     */
    public function updated(Storage $storage)
    {
        //
    }

    /**
     * Handle the Storage "deleted" event.
     *
     * @param \App\Models\Storage $storage
     * @return void
     */
    public function deleted(Storage $storage)
    {
        //
    }

    /**
     * Handle the Storage "restored" event.
     *
     * @param \App\Models\Storage $storage
     * @return void
     */
    public function restored(Storage $storage)
    {
        //
    }

    /**
     * Handle the Storage "force deleted" event.
     *
     * @param \App\Models\Storage $storage
     * @return void
     */
    public function forceDeleted(Storage $storage)
    {
        //
    }
}
