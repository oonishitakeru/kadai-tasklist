<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        /*$tasks=Task::all();
        
        return view('tasks.index',['tasks'=>$tasks,]);*/
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            // （後のChapterで他ユーザの投稿も取得するように変更しますが、現時点ではこのユーザの投稿のみ取得します）
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(5);
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }
        
        // dashboardビューでそれらを表示
        return view('dashboard', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task=new Task;
        
        return view('tasks.create',['task'=>$task,]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'status' => 'required|max:10',
            'content'=> 'required|max:255',
        ]);
        /*$task=new Task;
        $task->content=$request->content;
        $task -> status = $request->status;
        $task->save();
        */
        
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status'=>$request->status,
            'user_id'=>$request->id,
        ]);
        return redirect("/");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        
        $task=Task::findorFail($id);
        
        if(\Auth::id()===$task->user_id){
        
        return view('tasks.show',['task'=>$task]);
        }
        
        return redirect('/');
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task=Task::findorFail($id);
        
        if(\Auth::id()===$task->user_id){
        
        
        
        return view('tasks.edit',['task'=>$task]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $task=Task::findorFail($id);
        
        if(\Auth::id()===$task->user_id){
        
        $request->validate([
            'status' => 'required|max:10',
            'content' => 'required|max:255',
        ]);
        $task=Task::findorFail($id);
        
        $task -> content = $request -> content;
        $task -> status = $request -> status;
        $task->save();
        }
        
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task=Task::findorFail($id);
        
        if(\Auth::id()===$task->user_id){
        
        $task->delete();
        
        return redirect('/')->with('削除に成功しました');
        
        }
        
        return redirect('/')->with('権限がありません');
    }
}
