<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\Department;
//use App\Models\Ticket;
//use Illuminate\Http\JsonResponse;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Storage;
//
//
//// we don't use this controller on cms
//class TicketsController extends Controller
//{
//    protected $result=[];
//    protected $url;
//    public function addTicket(Request $request): JsonResponse
//    {
//   $this->result=     $request->validate([
//
//          'user_id'=>'required',
//          'department'=>'required',
//          'title'=>'required',
//       'priority'=>'required',
//           'picture',
//          'message'=>'required',
//          'status'=>'required'
//        ]);
//
//        if(!empty($request->picture)){
//            //get filename with extension
//            $filenamewithextension = $request->file('picture')->getClientOriginalName();
//
//            //get filename without extension
//            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
//
//            //get file extension
//            $extension = $request->file('picture')->getClientOriginalExtension();
//
//            //filename to store
//            $filenametostore = $filename.'_'.uniqid().'.'.$extension;
//
//            //Upload File to external server
//            Storage::disk('ftp')->put($filenametostore, fopen($request->file('picture'), 'r+'));
//
//            //Store $filenametostore in the database
//            $this->url=Storage::disk('ftp')->url('https://dl.poshtybanman.ir/upload/'.$filenametostore);
//        }
//
//    $this->result= Ticket::query()->create([
//            'user_id'=>$request->user_id,
//            'department_id'=>$request->department,
//            'title'=>$request->title,
//            'priority'=>$request->priority,
//             'picture'=> $this->url ,
//            'message'=>$request->message,
//            'status'=>$request->status,
//        ]);
//
//
//     return response()->json($this->result,201);
//}
//
//    public function addDepartment(Request $request): JsonResponse
//    {
//      $request->validate([
//           'name'=>'required|unique:departments,name'
//        ]);
//
//       $department= Department::query()->create([
//
//            'name'=>$request->name,
//
//        ]);
//        return response()->json($department,201);
//}
//
//    public function getTickets(): JsonResponse
//    {
//      $tickets=  Ticket::query()->join('departments','departments.id','=','tickets.department_id')
//          ->get(['tickets.*','departments.name']);
//
//      if(!$tickets){
//          return response()->json([
//              'message'=>'تیکتی وجود ندارد'
//          ]);
//      }
//
//      return response()->json($tickets);
//}
//
//
//
//}
