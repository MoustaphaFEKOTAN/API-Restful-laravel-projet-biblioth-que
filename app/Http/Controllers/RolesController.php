<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
      $role =  Roles::all();

      return response()->json($role);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $request->Validate([

    'nom' => 'required|string'
      ]);


      Roles::Create([
        'nom' => $request->nom,
        'slug' => (string) str::uuid(), 
      ]);

      return response()->json(['message' => 'Role créer avec succes']);

    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
       $role =  Roles::Where('slug',$slug)->firstOrFail();

       return response()->json($role);

    }

   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$slug)
    {
           $role =  Roles::Where('slug',$slug)->firstOrFail();

           $request->validate([

            'nom' => 'sometimes|required',
           ]);

           if($request->has('nom')){
$role->update();

return response()->json(['message' => 'Mis a jour effectuee']);
           };

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
            $role =  Roles::Where('slug',$slug)->firstOrFail();

            if($role){
                $role->delete();
            };
    }
}
