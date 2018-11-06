<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use Intranet\Entities\AlumnoFct;

class AlumnoFct extends Model
{

    use BatoiModels;
    protected $fillable = ['id', 'desde','hasta','horas'];
    
    protected $rules = [
        'id' => 'required',
        'desde' => 'date',
        'hasta' => 'date',
        'horas' => 'required|numeric'
    ];
    protected $inputTypes = [
        'id' => ['type' => 'hidden'],
    ];
       
    
    public function Alumno()
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }
    public function Fct()
    {
        return $this->belongsTo(Fct::class, 'idFct', 'id');
    }

    
    public function scopeMisFcts($query,$profesor=null,$activa=null)
    {
        $profesor = $profesor?$profesor:AuthUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor)->get()->toArray();
        $cicloC = Grupo::select('idCiclo')->QTutor($profesor)->first()->idCiclo;
        $colaboraciones = Colaboracion::select('id')->where('idCiclo',$cicloC)->get()->toArray();
        $fcts = $activa?Fct::select('id')->Activa($activa)->whereIn('idColaboracion',$colaboraciones)
                ->get()->toArray():Fct::select('id')->whereIn('idColaboracion',$colaboraciones)
                ->orWhere('asociacion',2)->get()->toArray();
        return $query->whereIn('idAlumno',$alumnos)->whereIn('idFct',$fcts);
    }
    
    public function scopeMisConvalidados($query,$profesor=null)
    {
        $profesor = $profesor?$profesor:AuthUser()->dni;
        $alumnos = Alumno::select('nia')->misAlumnos($profesor)->get()->toArray();
        $fcts = Fct::select('id')->Where('asociacion',2)->get()->toArray();
        return $query->whereIn('idAlumno',$alumnos)->whereIn('idFct',$fcts);
    }
    public function scopeEsFct($query){
        $fcts = Fct::select('id')->esFct()->get()->toArray();
        return $query->whereIn('idFct',$fcts);
    }
    public function scopeEsAval($query){
        $fcts = Fct::select('id')->esAval()->get()->toArray();
        return $query->whereIn('idFct',$fcts);
    }
    public function scopeEsDual($query){
        $fcts = Fct::select('id')->esDual()->get()->toArray();
        return $query->whereIn('idFct',$fcts);
    }
    
    
    public function getNombreAttribute(){
        return $this->Alumno->NameFull;
    }
    public function getPeriodeAttribute(){
        return $this->Fct->periode;
    }
    public function getQualificacioAttribute(){
        return isset($this->calificacion)?$this->calificacion?$this->calificacion==2?'Convalidat/Exempt': 'Apte' : 'No Apte' : 'No Avaluat';
    }
    public function getProjecteAttribute(){
        return isset($this->calProyecto) ? $this->calProyecto == 0 ? 'No presenta' : $this->calProyecto : 'No Avaluat';
    }
    public function getAsociacionAttribute(){
        return $this->Fct->asociacion;
    }
    public function getCentroAttribute(){
        return $this->Fct->Centro;
    }
    public function getInstructorAttribute(){
        return $this->Fct->XInstructor;
    }
    public function getDesdeAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
    public function getHastaAttribute($entrada)
    {
        $fecha = new Date($entrada);
        return $fecha->format('d-m-Y');
    }
}
