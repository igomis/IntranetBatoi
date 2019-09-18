<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Entities\Profesor;
use Intranet\Entities\FctConvalidacion;
use DB;
use Styde\Html\Facades\Alert;
use Intranet\Botones\Panel;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;
use Intranet\Jobs\SendEmail;
use Illuminate\Http\Request;

class FctAlumnoController extends IntranetController
{
    use traitImprimir;
    
    protected $perfil = 'profesor';
    protected $model = 'AlumnoFct';
    protected $gridFields = ['Nombre', 'Centro','Instructor','desde','hasta','horas','periode'];
    protected $profile = false;
    protected $titulo = [];
    protected $parametresVista = ['modal' => ['entreFechas']];



    public function search()
    {
        return AlumnoFctAval::misFcts()->esAval()->orderBy('idAlumno')->orderBy('desde')->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('alumnofct.delete'));
        $this->panel->setBoton('grid', new BotonImg('alumnofct.edit',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('alumnofct.show',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('alumnofct.pdf',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('index', new BotonBasico("fct.create", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("alumnofct.convalidacion", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pg0301.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0301.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0401.print",['id' => '401', 'roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0402.print",['id' => '402', 'roles' => config('roles.rol.tutor')]));
        //$this->panel->setBoton('index', new BotonBasico("fct.pr0601.print",['roles' => config('roles.rol.tutor')]));
        Session::put('redirect', 'FctAlumnoController@index');
    }
        //

    public function nuevaConvalidacion()
    {
        $elemento = new FctConvalidacion();
        $default = $elemento->fillDefautOptions(); // ompli caracteristiques dels camps
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('elemento', 'default', 'modelo'));
    }

    public function storeConvalidacion(Request $request)
    {
        $idFct = DB::transaction(function() use ($request){
            $idAlumno = $request['idAlumno'];
            $elementos = FctConvalidacion::where('idColaboracion',$request->idColaboracion)
                    ->where('asociacion',$request->asociacion)
                    ->get();
            $id = null;
            foreach ($elementos as $elemento){
                    if ($elemento->Periode == PeriodePractiques(Hoy())){
                        $id = $elemento->id;
                        break;
                    }
                }
            if (!$id){ 
                $elemento = new FctConvalidacion();
                $this->validateAll($request, $elemento);
                $id = $elemento->fillAll($request);
            } 
            $elemento->Alumnos()->attach($idAlumno,['desde'=> FechaInglesa(Hoy()),'horas'=>$request->horas,'calificacion' => 2,'correoAlumno'=>1]);
            
            return $id;
        });
        
        return $this->redirect();
    }
    
    public function show($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        return redirect("/fct/$fct->idFct/show");
    }
    
    public function pdf($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        //dd($fct);
        $secretario = Profesor::find(config('contacto.secretario'));
        $director = Profesor::find(config('contacto.director'));
        $dades = ['date' => FechaString($fct->hasta),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];
        
        $pdf = $this->hazPdf('pdf.fct.alumne', [$fct], $dades);
        return $pdf->stream();
    }
    public function email($id)
    {
        // CARREGANT DADES
        $elemento = AlumnoFct::findOrFail($id);


        // MANE ELS TREBALLS
        if ($elemento->Alumno->email != ''){
            $remitente = ['email' => AuthUser()->email, 'nombre' => AuthUser()->FullName, 'id' => AuthUser()->dni];
            dispatch(new SendEmail($elemento->Alumno->email, $remitente, 'email.fct.alumno', $elemento));
            Alert::info('Correu enviat');
            return back();
        }

        Alert::info("L'alumne no té correu. Revisa-ho");
        return back();
    }
    
    public function pg0301($id){
       $fct = AlumnoFct::find($id);
       $fct->pg0301 = $fct->pg0301?0:1;
       $fct->save();
       return redirect()->action('PanelPG0301Controller@indice',['id' => $fct->Grup]);
    }

} 