<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    const PROJECT_DIRECTOR_TYPE = 'PD';
    const DEPUTY_PROJECT_DIRECTOR_TYPE = 'DPD';
    const AGRICULTURE_FACILITATOR_TYPE = 'AF';
    const AGRICULTURE_SPECIALIST_TYPE = 'AS';
    const IDCB_TYPE = 'IDCB';
    const MandE_TYPE = 'MandE';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'depend_dpd_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // -----------------------middleware project director---------------------------------------------------------
    public function isProjectDirector(){
        return $this->role === self::PROJECT_DIRECTOR_TYPE;
    }

    // -----------------------middleware deputy project director---------------------------------------------------------
    public function isDeputyProjectDirector(){
        return $this->role === self::DEPUTY_PROJECT_DIRECTOR_TYPE;
    }

    // -----------------------middleware Agriculture Facilitator---------------------------------------------------------
    public function isAgricultureFacilitator(){
        return $this->role === self::AGRICULTURE_FACILITATOR_TYPE;
    }

    // -----------------------middleware Agriculture Specialist---------------------------------------------------------
    public function isAgricultureSpecialist(){
        return $this->role === self::AGRICULTURE_SPECIALIST_TYPE;
    }

    // -----------------------middleware IDCB---------------------------------------------------------
    public function isIDCB(){
        return $this->role === self::IDCB_TYPE;
    }

    // -----------------------middleware M&E---------------------------------------------------------
    public function isMandE(){
        return $this->role === self::MandE_TYPE;
    }

    //------------------------ has many locations--------------------------
    public function trackingLogs(){
        return $this->hasMany('App\TrackingDetails');
    }
}
