<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Wildside\Userstamps\Userstamps;

/**
 * @property \Illuminate\Contracts\Database\Eloquent\Builder $builder
 */
class Agent extends Model
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
                $builder->where('id', $modelHasRole->agent_id);
            }

            if ($auth->hasRole('karan') || $auth->hasRole('kaur')) {
                $builder->where('id', $modelHasRole->branch->agent_id);
            }
        });
    }

    /**
     * Get the branches for the agent.
     */
    public function branches(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Branch::class);
    }

    // add public condition to the query result
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
