<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Specialty;

use App\Http\Controllers\Controller;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialities = Specialty::all();
        return view('specialties.index', compact('specialities'));
    }

    public function create()
    {
        return view('specialties.create');
    }

    private function performValidation($request)
    {
        $rules = [
            'name' => 'required|min:3'
        ];
        $messages = [
            'name.required' => 'Es necesario ingresar un nombre.',
            'name.min'      => 'Como mínimo el nombre debe tener 3 caracteres.'
        ];
        $this->validate($request, $rules, $messages);
    }

    public function store(Request $request)
    {
        $this->performValidation($request);
        //dd($request->all());
        $specialty = new Specialty();
        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        $notification = 'La especialidad se ha registrado correctamente.';
        return redirect('/specialties')->with(compact('notification'));
    }

    public function edit(Specialty $specialty)
    {
        return view('specialties.edit', compact('specialty'));
    }

    public function update(Request $request, Specialty $specialty)
    {
        $this->performValidation($request);
        //dd($request->all());
        $specialty->name = $request->input('name');
        $specialty->description = $request->input('description');
        $specialty->save();

        $notification = 'La especialidad se ha actualizado correctamente.';
        return redirect('/specialties')->with(compact('notification'));
    }

    public function destroy(Specialty $specialty)
    {
        $nombreElimined = $specialty->name;
        $specialty->delete();
        $notification = 'La especialidad '. $nombreElimined .' se ha eliminado correctamente.';
        return redirect('/specialties')->with(compact('notification'));
    }
}
