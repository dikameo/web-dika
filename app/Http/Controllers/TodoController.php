<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $q = Todo::query();
        if($s = $req->query('search')){
            $q->where('title', 'like', "%$s%")
              ->orWhere('description', 'like', "%$s%");
        }

        if($status = $req->query('status'))
            $q->where('status', $status);

        if($category = $req->query('category'))
            $q->where('category', $category);

        if($priority = $req->query('priority'))
            $q->where('priority', $priority);

        $todos = $q->paginate($req->integer('limit', 10));
        return response()->json($todos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,done',
            'due_date' => 'nullable|date',
            'priority' => 'required|integer|min:1|max:3',
            'category' => 'required|in:personal,work,others',
        ]);
        $todo = Todo::create($data);
        return response()->json($todo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        return response()->json($todo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        //
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:150',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|in:pending,in_progress,done',
            'due_date' => 'sometimes|nullable|date',
            'priority' => 'sometimes|required|integer|min:1|max:3',
            'category' => 'sometimes|required|in:personal,work,others',
        ]);
        $todo->update($data);
        return response()->json($todo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->json(['message' => 'Todo berhasil dihapus','id' => $todo->id], 200);
    }
}
