<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Closure;

class CheckTestHash
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hash = $request->get('hash');
        $task = Task::where('link', $hash)->whereHas('papers', function($q) use ($request){
            $q->where('id', $request->get('test_id'));
        })->first();

        if($task){
            return $next($request);
        }

        return ajaxResponse(ERROR, 'go home');
    }
}
