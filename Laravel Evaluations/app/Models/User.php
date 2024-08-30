<?php

namespace App\Models;

use App\Repositories\MailsRepository;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Yajra\Acl\Traits\HasRole;

class User extends Authenticatable
{
    use HasRole;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'city',
        'verified',
        'phone',
        'client_id',
        'deleted_at',
        'disabled_from',
        'disabled_to',
        'notifications'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     *  Extra attributes for user model
     *
     * @var array
     */
    protected $appends = ['is_active_now'];

    /**
     * @var string
     */
    protected $table = 'users';
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @param $notifications
     * @return mixed
     */
    public function getNotificationsAttribute($notifications)
    {
        if (is_null($notifications)) {
            return [];
        }
        return json_decode($notifications, true);
    }

    /**
     * @param $value
     */
    public function setNotificationsAttribute($value)
    {
        $this->attributes['notifications'] = json_encode($value);
    }

    /**
     * Send a password reset email to the user
     */
    public function sendPasswordResetNotification($token)
    {
        $attributes = [
            'route_reset' => url(config('app.url') . route('password.reset', $token, false))
        ];

        $emailService = new EmailService(new MailsRepository(new MailTemplate()));
        $attributes = array_merge($attributes, $this->attributes);
        $emailService->sendEmail($attributes, MAIL_FORGOT);

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allRoles()
    {
        return $this->hasMany('App\Models\UserRole');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups()
    {
        return $this->hasMany('App\Models\UserGroup');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany('App\Models\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany('App\Models\Task', 'assessor_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function following()
    {
        return $this->hasMany('App\Models\TaskFollower');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectsParticipating()
    {
        return $this->hasMany('App\Models\ProjectParticipant');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\UserStatus', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clients()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paperAnswers()
    {
        return $this->hasMany('App\Models\PaperAnswers');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasksAdded()
    {
        return $this->hasMany('App\Models\Task', 'added_by_id');
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    /**
     * @return string
     */
    public function getFullNameEmailAttribute()
    {
        return $this->first_name . " " . $this->last_name . ": " . $this->email;
    }


    /**
     * Checks if user is disabled at the moment
     *
     * @return bool
     */
    public function getIsActiveNowAttribute()
    {

        $isInactive = false;

        foreach ($this->inactivities()->get() as $inactivity) {

            $disabled_from = Carbon::parse($inactivity->date_from);
            $disabled_to = Carbon::parse($inactivity->date_to);

            if (Carbon::now()->between($disabled_from, $disabled_to)){
                $isInactive = true;
                break;
            }
        }

        return !$isInactive;
    }


    /**
     *  Gets only active now users
     *
     * @param $query
     */
    public function scopeActive($query)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $query->whereDoesntHave('inactivities', function ($q) use ($now) {
            $q->where('date_from', '<=', $now);
        });
    }

    /**
     * @param $slugs
     * @return bool
     */
    public function hasOnlyRole($slugs)
    {
        return $this->hasRole($slugs) && $this->roles()->count() == 1;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inactivities()
    {
        return $this->hasMany('App\Models\AssessorInactivity')
            ->where('date_to', '>=', Carbon::now())->orderBy('date_from', 'asc');
    }
    
}
