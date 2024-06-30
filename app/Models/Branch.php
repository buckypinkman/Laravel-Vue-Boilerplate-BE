<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Branch extends Model
{
    use HasFactory, Userstamps, SoftDeletes;

    protected $guarded = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('ancient', function (Builder $builder) {
            $auth = auth()->user();
            $role = $auth->roles->first();

            $modelHasRole = ModelHasRole::where([
                'model_type' => 'App\Models\User',
                'model_id' => $auth->id,
                'role_id' => $role->id
            ])
            ->with([
                'agent',
                'branch'
            ])
            ->first();

            if ($auth->hasRole('kadis') && $auth->hasRole('kabag')) {
                return;
            }

            if ($auth->hasRole('agent') || $auth->hasRole('kasi')) {
                $builder->where('agent_id', $modelHasRole->agent_id);
            }

            if ($auth->hasRole('karan') || $auth->hasRole('kaur')) {
                $builder->where('id', $modelHasRole->branch_id);
            }
        });
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
