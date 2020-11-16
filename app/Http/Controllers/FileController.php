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
            ['attachments' => 'required']);
        if ($validation->passes()) {
            $files = $request->attachments;
            $html = '';
            $ids = array();

            $path = 'public/attachments/' . $request->project_id;
            Storage::makeDirectory($path);

            foreach ($files as $file) {
                $file_name = $file->getClientOriginalName();

                $saved_file = new File();
                $saved_file->name = $file_name;
                $saved_file->filearea = 'project_update';
                $saved_file->itemid = 0;
                $saved_file->filepath = Storage::putFile($path, $file);
                $saved_file->save();

                $url = Storage::url($path . '/' . $file_name);
                $html .= '<span id="uploaded_file" class="d-block"><input type="hidden" name="file_id[]" value="' . $saved_file->id . '"><a href="' . $url . '">' . $file_name . ' </a><button type="button" name="remove" class="btn btn-outline-danger btn-sm remove"><i
                                        class="far fa-trash-alt"></i></button></span>';
                $ids[] = $saved_file->id;
            }
            return Response()->json([
                'message' => 'File(s) uploaded',
                'attachments' => $html,
                'class_name' => 'alert-success',
                'file_ids' => json_encode($ids)
            ]);
        } else {
            return Response()->json([
                "message" => $validation->errors()->all(),
                "attachments" => '',
                "class_name" => 'alert-danger'
            ]);
        }
    }
}
