<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    function store(Request $request)
    {
        $validation = \Validator::make($request->all(),
            ['file' => 'required|mimes:jpg,png,doc,docx,pdf,txt|max:2048']);
        if ($validation->passes()) {
            $file = $request->file('file');
            $file_name = $file->getClientOriginalName();
            $path = 'public/attachments/' . $request->get('project_id');
            Storage::makeDirectory($path);
            Storage::putFileAs($path, $request->file('file'), $file_name);
            $saved_file = new File();
            $saved_file->name = $file_name;
            $saved_file->filearea = 'project_update';
            $saved_file->itemid = 0;
            $saved_file->filepath = $path;
            $saved_file->save();
            $url = Storage::url($path . '/' . $file_name);
            return Response()->json([
                'message' => 'File uploaded',
                'uploaded_file' => '<a href="' . $url . '">' . $file_name . '</a>',
                'class_name' => 'alert-success',
                'file_id' => $saved_file->id
            ]);
        } else {
            return Response()->json([
                "message" => $validation->errors()->all(),
                "uploaded_file" => '',
                "class_name" => 'alert-danger'
            ]);
        }
    }
}
