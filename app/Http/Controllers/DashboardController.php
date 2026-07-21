<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        return view('pages.dashboard');
    }
	
	public function addImage(Request $request) {
		if ($request->hasFile('file')) {
			$files = $request->file('file');
			
			$images = [];
			
			if (!is_array($files)) {
				$name = $files->getClientOriginalName();
				$files->move('assets/site/img/other', $name);
				$images[] = 'assets/site/img/other/' . $name;
			} else {
				foreach ($files as $file) {
					$name = $file->getClientOriginalName();
					$file->move('assets/site/img/other', $name);
					$images[] = 'assets/site/img/other/' . $name;
				}
			}
			
			return response()->json($images);
		}
	}
	
	public function addImageSite(Request $request) {
		$validate = \Validator::make($request->all(), [
			'file.*' => 'required|file|max:1024|mimes:png,jpg,pdf,xlsx,gif,docx'
		]);
		
		if ($validate->fails()) {
			return response()->json(['errors' => $validate->errors()->all()]);
		}
		
		if ($request->hasFile('file')) {
			$files = $request->file('file');
			
			$images = [];
			
			if (!is_array($files)) {
				$name = $files->getClientOriginalName();
				$files->move('assets/site/img/other', $name);
				$images[] = 'assets/site/img/other/' . $name;
			} else {
				foreach ($files as $file) {
					$name = $file->getClientOriginalName();
					$file->move('assets/site/img/other', $name);
					$images[] = 'assets/site/img/other/' . $name;
				}
			}
			
			return response()->json($images);
		}
	}
}
