<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait OwnsRecords
{
    protected function authorizeOwner(Request $request, $model): void
    {
        abort_unless((int) $model->user_id === (int) $request->user()->id, 403);
    }
}
