<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;
use Veelasky\LaravelHashId\Eloquent\HashableId;

use App\Notifications\PasswordResetNotif;
use App\Notifications\SendEmailVerification;

use App\Traits\PaginatesTrait;
use App\Traits\HasTeam;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HashableId, PaginatesTrait, HasTeam;

    /**
     * The attributes that are mass assignable.
     * I am switching to $fillable for better security and to include your new fields.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "username",
        "full_name",
        "role",
        "email",
        "password",
        "fails",
        "email_verified_at",
        "disabled_at"
    ];

    /**
     * The attributes that should be guarded. The attributes are not mass assignable.
     * We will use $fillable instead, so this is no longer needed.
     *
     * @var array<int, string>
     */
    // protected $guarded = ["id", "remember_token", "created_at", "updated_at"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            "email_verified_at" => "datetime",
            "disabled_at" => "datetime",
            "password" => "hashed",
        ];
    }

    // --- NEW RELATIONSHIPS FROM YOUR ERD ---

    /**
     * Get the repair logs associated with the user.
     */
    public function repairLogs()
    {
        return $this->hasMany(RepairLog::class);
    }

    /**
     * Get the status history records associated with the user.
     */
    public function statusHistories()
    {
        return $this->hasMany(StatusHistory::class);
    }

    // --- KEEPING ALL YOUR EXISTING METHODS ---

    public function isSuperman(): bool {
        return $this->hasRole(config("mitd.superman"));
    }

    public function profile() {
        return $this->hasOne(Profile::class);
    }

    public function team(): BelongsTo {
        if (config("permission.teams")) {
            return $this->belongsTo(config("mitd.permission.teams_provider"), config("permission.column_names.team_foreign_key"), "id");
        }
        return $this->morphTo();
    }

    public function roles_all(): BelongsToMany {
        return $this->morphToMany(
            config("permission.models.role"),
            "model",
            config("permission.table_names.model_has_roles"),
            config("permission.column_names.model_morph_key"),
            config("permission.column_names.role_pivot_key") ?: "role_id"
        );
    }

    public function resetFailedLoginAttempts() {
        $this->fails = 0;
        $this->save();
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token) {
        $this->notify(new PasswordResetNotif($token));
    }

    public function sendEmailVerificationNotification(): void {
        $this->notify(new SendEmailVerification());
    }

    public function getSessionTeam() {
        if (config("permission.teams")) {
            return app(config("mitd.permission.teams_provider"))::find(getPermissionsTeamId() ?? $this->team_id);
        }
        return null;
    }

    public function repairLogs()
    {
        return $this->hasMany(RepairLog::class);
    }

    /**
     * Get the status history records associated with the user.
     */
    public function statusHistories()
    {
        return $this->hasMany(StatusHistory::class);
    }
}
