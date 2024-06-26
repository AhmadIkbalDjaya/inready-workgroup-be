<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Events\SetCreatedBy;
use App\Models\Events\SetUpdatedBy;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];
    protected $guarded = ["id"];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    protected $dispatchesEvents = [
        "creating" => SetCreatedBy::class,
        "saving" => SetUpdatedBy::class,
    ];

    public function articles_created(){
        return $this->hasMany(Article::class, 'created_by');
    }
    public function articles_updated(){
        return $this->hasMany(Article::class, 'updated_by');
    }
    
    public function agendas_created(){
        return $this->hasMany(Agenda::class, 'created_by');
    }
    public function agendas_updated(){
        return $this->hasMany(Agenda::class, 'updated_by');
    }
    
    public function activities_created(){
        return $this->hasMany(Activity::class, 'created_by');
    }
    public function activities_updated(){
        return $this->hasMany(Activity::class, 'updated_by');
    }

    public function members_created(){
        return $this->hasMany(Member::class, 'created_by');
    }
    public function members_updated(){
        return $this->hasMany(Member::class, 'updated_by');
    }

    public function works_created(){
        return $this->hasMany(Work::class, 'created_by');
    }
    public function works_updated(){
        return $this->hasMany(Work::class, 'updated_by');
    }
    
    public function sliders_created(){
        return $this->hasMany(Slider::class, 'created_by');
    }
    public function sliders_updated(){
        return $this->hasMany(Slider::class, 'updated_by');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function editor() {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function member() {
        return $this->belongsTo(Member::class, 'member_id', "id");
    }

}
