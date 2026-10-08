<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB; // Required for the position query

class User extends Authenticatable
{
    use Notifiable;

    // 1. Tell Laravel the correct table name
    protected $table = 'userlist';

    // 2. Tell Laravel the primary key is login_id, not id
    protected $primaryKey = 'login_id';

    // 3. Disable standard created_at/updated_at timestamps
    public $timestamps = false; 

    // 4. Allow these columns to be filled during SSO login/creation
    protected $fillable = [
        'login_username',
        'name',
        'login_pwd',
        'role',
        'login_stamp'
    ];

    // Tell Laravel which column is used for the password
    public function getAuthPassword()
    {
        return $this->login_pwd;
    }

    /**
     * Get the user's name without parentheses
     * Usage in blade: {{ auth()->user()->clean_name }}
     */
    public function getCleanNameAttribute()
    {
        return trim(explode('(', $this->name)[0]);
    }

/**
     * Fetch the user's position from the elatihanv3 database
     */
    public function getPositionAttribute()
    {
        $cleanName = $this->clean_name;

        $wildcardName = str_replace(' ', '%', trim($cleanName));

        $peribadi = DB::connection('elatihan')
            ->table('peribadi')
            ->where('namapegawai', 'LIKE', '%' . $wildcardName . '%')
            ->first();

        // Returns 'skim_khidmat' if found, otherwise falls back to the user's 'role'
        return $peribadi ? $peribadi->skim_khidmat : $this->role;
    }
}