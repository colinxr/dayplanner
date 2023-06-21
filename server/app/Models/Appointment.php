<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $dates = [
        'startDateTime',
        'endDateTime',
    ];

    protected $appended = [
        'duration'
    ];

    ///
    // Relationships
    ///
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d\TH:i:sO');
    }

    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->startDateTime);
        $end = Carbon::parse($this->endDateTime);

        return $end->diffInMinutes($start) / 60;
    }

    public function setStartDateTimeAttribute($value)
    {
        $this->attributes['startDateTime'] = Carbon::createFromFormat('Y-m-d\TH:i:sO', $value);
    }

    public function setEndDateTimeAttribute($value)
    {
        $this->attributes['endDateTime'] = Carbon::createFromFormat('Y-m-d\TH:i:sO', $value);
    }

    public function getStartDateTimeAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d\TH:i:sO');
    }

    public function getEndDateTimeAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d\TH:i:sO');
    }

    /// Scope

    public function scopeUpcoming($query, $timeToQuery = null)
    {
        $time = !$timeToQuery ? Carbon::now() : $timeToQuery;

        return $query->where('startDateTime', '>', $time);
    }
}
