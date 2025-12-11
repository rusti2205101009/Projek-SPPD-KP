<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;

trait FilterByUser
{
    public function scopeByUser(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            return $query;
        }

        $model = $query->getModel();

        if (Schema::hasColumn($model->getTable(), 'user_id')) {
            return $query->where('user_id', $user->id ?? 0);
        }

        if (method_exists($model, 'spt')) {
            return $query->whereHas('spt', function ($q) use ($user) {
                $q->where('user_id', $user->id ?? 0);
            });
        }

        if (method_exists($model, 'spts')) {
            return $query->whereHas('spts', function ($q) use ($user) {
                $q->where('user_id', $user->id ?? 0);
            });
        }

        return $query->whereRaw('1 = 0'); 
    }
}
