<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Task;
use Carbon\Carbon;


class TaskController extends Controller
{
    public function index(Request $request) {

        $perPage =  10;
        if($request->get('options') && is_numeric($request->get('options')['itemsPerPage']))
            $perPage =  $request->get('options')['itemsPerPage'];

        $tasks = Task::when(
            $request->search,
            fn ($q) => $q->where('title', 'like', "%{$request->search}%")
            ->orWhere('description', 'like', "%{$request->search}%")
        )
        ->paginate($perPage);

        if($perPage == -1 || $perPage == '-1'){
            $perPage = $tasks->count();
        }

        return new JsonResponse([
            'data'  =>  (clone $tasks)->toArray(),
            'total' =>  $tasks->total(),
            'perPage' =>  $tasks->perPage(),
            'currentPage' =>  $tasks->currentPage(),
            'lastPage' =>  $tasks->lastPage(),
            'next' =>  $tasks->nextPageUrl(),
            'previous' =>  $tasks->previousPageUrl(),
        ], Response::HTTP_OK);
    }
    
    public function show(Request $request, Task $task) {
        
        return $task;
    }
    
    public function statistics(Request $request) {

        $tasks = Task::select(
            \DB::raw('count(id) as count'), 
            \DB::raw('
                CASE 
                WHEN status = 1 THEN "finished" 
                Else "in-progress"
                END AS status
            ')
        )
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();
        $allTasks = 0;
        foreach ($tasks as $key => $value) {
            $allTasks += $value;
        }

        $statistics = [];
        $statistics['finished'] = isset($tasks['finished']) ? ceil(($tasks['finished']/$allTasks)*100) : 0;
        $statistics['in-progress'] = isset($tasks['in-progress']) ? ceil(($tasks['in-progress']/$allTasks)*100) : 0;
        
        return $statistics;
    }

    public function save(Request $request) {

        $message = 'Task added Successfully';
        $task = new Task();
        if($request->id>0) {
            $message = 'Task updated Successfully';
            $task = Task::find($request->id);
        }

        $task->title = $request->title;
        $task->description = $request->description;
        $task-> due_date = Carbon::parse($request->due_date);
        $task->save();

        return response()->json(['task' => $task, 'message' => $message, 'status' => true]);
    }

    public function action(Task $task, Request $request) {

        $message = '';
        if($request->action == 'in-progress') {
            $task->status = 0;
            $message = 'Task In Progress';
        }
        else if($request->action == 'finished') {
            $task->status = 1;
            $message = 'Task Finished';
        }

        $task->save();

        return response()->json(['task' => $task, 'message' => $message, 'status' => true]);
    }

    public function delete($id) {

        $record = Task::find($id);
        if (!$record) {
            return response()->json(['message' => 'Record not found', 'status' => false], 404);
        }
        $record->delete();
        return response()->json(['message' => 'Task deleted successfully', 'status' => true]);
    }
}
