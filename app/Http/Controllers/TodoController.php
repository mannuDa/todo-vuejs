<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;

class TodoController extends Controller
{
    public function Todo(request $req, $type, $id=NULL)
    {
        switch ($type) {
            case 'list':
                if ($req->isMethod('get')) {
                    $task = Todo::all();
                    return response()->json($task, 201);
                }
                break;
            case 'store':
                if($req->isMethod('post')){
                    $validatedData = $req->validate([
                        'title' => 'required|unique:todos',
                    ]);
                    if (!$validatedData) {
                        return response()->json([
                            'errors' => $validatedData->errors(),
                        ], 422);
                    }
            
                    $task = Todo::create($validatedData+ ['completed' => 'no']);
                    
                    return response()->json($task, 201);

                }
                break;
            case 'edit':
                if($req->isMethod('put')){
                    $task = Todo::findOrFail($id);
                    $task->update(['completed' => 'yes']);
                    return response()->json($task);

                }
                break;
            case 'destroy':
                if($req->isMethod('delete')){
                    $task = Todo::findOrFail($id)->delete();            
                    return response()->json(null, 204);

                }
                break;
        }
    }
}
