<?php

namespace App\Models;

use App\Enums\RolesEnum;
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
        static::addGlobalScope('BasedOnRoles', function (Builder $builder) {
            $auth = auth()->user();
            $role = $auth->roles->first();

            $modelHasRole = ModelHasRole::where([
                'model_type' => 'App\Models\User',
                'model_id' => $auth->id,
                'role_id' => $role->id
            ])
            ->first();

            $branch = Branch::whereId($modelHasRole->branch_id)->first();

            if ($auth->hasRole(RolesEnum::KADIS->value) && $auth->hasRole(RolesEnum::KABAG->value)) {
                return;
            }

            if ($auth->hasRole(RolesEnum::AGEN->value) || $auth->hasRole(RolesEnum::KASI->value)) {
                $builder->where('id', $modelHasRole->agent_id);
            }

            if ($auth->hasRole(RolesEnum::KARAN->value) || $auth->hasRole(RolesEnum::KAUR->value)) {
                $builder->where('id', $branch->agent_id);
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
