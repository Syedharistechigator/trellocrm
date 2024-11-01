<?php

namespace App\Traits;

use App\Models\LogAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait LogActivityTrait
{
    protected static $logEvents = ['created', 'updated', 'deleted'];

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootLogActivity()
    {
        $logEvents = static::getLogEvents();
        if (!empty($logEvents)) {

            foreach ($logEvents as $eventName) {
                static::$eventName(function (Model $model) use ($eventName) {
                    try {

                        if (Auth::check()) {
                            $actor_id = Auth::id();
                            $actor_type = get_class(Auth::user() ?? "");
                            if (Auth::user()->type === 'tm-client' && Auth::user()->clientid) {
                                $actor_id = Auth::user()->clientid;
                                $actor_type = 'App\Models\Client';
                            }
                        } elseif (isset($model->creator_id)) {
                            $actor_id = $model->creator_id;
                            $actor_type = $model->creator_type;
                        }
                        $logData = [
                            'actor_id' => $actor_id??null,
                            'actor_type' => $actor_type??null,
                            'action' => $eventName,
                            'loggable_id' => $model->id,
                            'loggable_type' => get_class($model),
                            'previous_record' => null,
                        ];

                        if ($eventName === 'updated' && $model->shouldBeLogged()) {
                            $logData['previous_record'] = json_encode($model->getOriginal());
                        } else {
                            $logData['previous_record'] = json_encode($model->getAttributes());
                        }
                        $log = new LogAction($logData);
                        $model->logs()->save($log);
                    } catch (\Exception $e) {
                        Log::error('Error saving log action: ' . $e->getMessage());
                    }
                });
            }
        }
    }

    /**
     * Get the events to be logged.
     *
     * @return array
     */
    protected static function getLogEvents()
    {
        return isset(static::$logEvents) ? static::$logEvents : [];
    }

    public function logs()
    {
        return $this->morphMany(LogAction::class, 'loggable')->orderBy('created_at', 'desc');
    }
}
