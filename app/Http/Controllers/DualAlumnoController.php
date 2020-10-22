<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Grupo;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use DB;

use Illuminate\Support\Facades\Session;
use mikehaertl\pdftk\Command;
use mikehaertl\pdftk\Pdf;
use Jenssegers\Date\Date;

/**
 * Class DualAlumnoController
 * @package Intranet\Http\Controllers
 */
class DualAlumnoController extends FctAlumnoController
{
    use traitImprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'AlumnoFct';
    /**
     * @var array
     */
    protected $gridFields = ['Nombre', 'Centro','Instructor','desde','hasta','horas'];
    /**
     * @var bool
     */
    protected $profile = false;
    /**
     * @var array
     */
    protected $titulo = [];
    /**
     * @var array
     */

    /**
     * @return mixed
     */
    public function search()
    {
        return AlumnoFct::misDual()->orderBy('idAlumno')->orderBy('desde')->get();
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('dual.delete'));
        $this->panel->setBoton('grid', new BotonImg('dual.edit'));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.covid',['img'=>'fa-file-word-o']));
        $this->panel->setBoton('grid', new BotonImg('dual.firma',['img'=>'fa-file-word-o']));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.anexe_vii'));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.anexe_va'));
        $this->panel->setBoton('grid', new BotonImg('dual.pdf.anexe_vb'));
        $this->panel->setBoton('grid', new BotonImg('dual.anexeXIII',['img'=>'fa-file-pdf-o']));
        $this->panel->setBoton('index', new BotonBasico("dual.create", ['class' => 'btn-info']));
        $this->panel->setBoton('index', new BotonBasico("dual.anexeVI", ['class' => 'btn-info','id' => 'anexoVI']));
        $this->panel->setBoton('index', new BotonBasico("dual.anexeXIV", ['class' => 'btn-info','id' => 'anexoXIV']));

        Session::put('redirect', 'DualAlumnoController@index');
    }
        //


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        return redirect("/fct/$fct->idFct/show");
    }
    

    /**
     * @param $id
     * @param string $informe
     * @return mixed
     */
    public function informe($id, $informe='anexe_vii')
    {
        $informe = 'dual.'.$informe;
        $fct = AlumnoFct::findOrFail($id);
        $secretario = Profesor::find(config('contacto.secretario'));
        $director = Profesor::find(config('contacto.director'));
        $dades = ['date' => FechaPosterior($fct->hasta),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'codigo' => config('contacto.codi'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];


        $orientacion = substr($informe,0,5)==='anexe'?'landscape':'portrait';
        $pdf = $this->hazPdf($informe, $fct,$dades,$orientacion,'a4',10);
        return $pdf->stream();
    }


    protected function zipFirmaConveni($id){
        $fct = AlumnoFct::findOrFail($id);
        $zip_file = "dual_".$fct->Alumno->dualName.".zip";
        $zip_local = $fct->Fct->Centro."/020_FaseFirmaConveni_".$fct->Alumno->dualName."/";

        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFile($this->printDOC4($id),$zip_local."doc4.pdf");
        $zip->addFile($this->printDOC1($id),$zip_local."doc4.pdf");
        $zip->close();

        return response()->download($zip_file);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function printDOC4($id)
    {
        $file = storage_path("tmp/dual$id/doc4".'.pdf');
        if (!file_exists($file)){
            $fct = AlumnoFct::findOrFail($id);
            $grupo = $fct->Alumno->Grupo->first();
            $horario = Horario::HorarioGrupo($grupo->codigo);
            $turno = isset($horario['L'][2]) ? 'mati':'vesprada';
            $ciclo = $fct->Fct->Colaboracion->Ciclo->vliteral;
            $dades = compact('grupo','ciclo','turno');
            $pdf = $this->hazPdf('dual.doc4', $horario,$dades,'portrait','a4',10);
            $pdf->save($file);
        }
        return $file;
    }


    public function printAnexeXIII($id){
        $pdf = new Pdf('fdf/ANEXO_XIII.pdf');
        $pdf->fillform($this->makeArrayPdfAnexoXIII($id))
            ->send("dualXIII_$id".'.pdf');
        return $this->redirect();
    }

    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfAnexoXIII($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        $array[1] = Profesor::find(config('contacto.secretario'))->fullName;
        $array[2] = config('contacto.nombre');
        $array[3] = config('contacto.codi');
        $array[4] = $fct->Alumno->fullName;
        $array[5] = $fct->Alumno->dni;
        $array[6] = $fct->horas;
        $array[7] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[8] = $array[1];
        $array[9] = config('contacto.nombre');
        $array[10] = config('contacto.codi');
        $array[11] = $fct->Alumno->fullName;
        $array[12] = $fct->Alumno->dni;
        $array[13] = $fct->horas;
        $array[14] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[15] = $fct->Fct->Centro;
        $array[16] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[17] = $fct->horas;
        $array[18] = $fct->desde."/".$fct->hasta;
        $array[19] = 1;
        $array[20] = 'Dissenyador web';
        $array[27] = config('contacto.poblacion');
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[28] = $fc1->format('d');
        $array[29] = $fc1->format('F');
        $array[30] = $fc1->format('Y');
        $array[31] = $array[1];
        $array[32] = Profesor::find(config('contacto.director'))->fullName;

        $array[33] = $array[1];
        $array[34] = config('contacto.nombre');
        $array[35] = config('contacto.codi');
        $array[36] = $fct->Alumno->fullName;
        $array[37] = $fct->Alumno->dni;
        $array[38] = $fct->horas;
        $array[39] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array[40] = $array[1];
        $array[41] = config('contacto.nombre');
        $array[42] = config('contacto.codi');
        $array[43] = $fct->Alumno->fullName;
        $array[44] = $fct->Alumno->dni;
        $array[45] = $fct->horas;
        $array[46] = $fct->Fct->Colaboracion->Ciclo->cliteral;
        $array[47] = $fct->Fct->Centro;
        $array[48] = $fct->Fct->Colaboracion->Centro->direccion;
        $array[49] = $fct->horas;
        $array[50] = $fct->desde."/".$fct->hasta;
        $array[51] = 1;
        //$array[52] = 'Dissenyador web';
        $array[53] = config('contacto.poblacion');
        $fc1 = new Date();
        Date::setlocale('ca');
        $array[54] = $fc1->format('d');
        $array[55] = $fc1->format('F');
        $array[56] = $fc1->format('Y');
        $array[57] = $array[1];
        $array[58] = Profesor::find(config('contacto.director'))->fullName;

        return $array;
    }

    public function printDOC1($id){
        $file = storage_path("tmp/dual$id/doc1".'.pdf');
        if (!file_exists($file)) {
            $pdf = new Pdf('fdf/DOC_1.pdf');
            $pdf->fillform($this->makeArrayPdfDOC1($id))
                ->saveAs($file);
        }
        return $file;
    }

    /**
     * @param $array
     * @return mixed
     */
    private function makeArrayPdfDOC1($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        $array['Texto3'] = config('contacto.nombre');
        $array['Texto5'] = config('contacto.codi');
        $array['Texto6'] = config('contacto.telefono');
        $array['Texto7'] = config('contacto.telefono');
        $array['Texto8'] = config('contacto.direccion');
        $array['Texto9'] = config('contacto.poblacion');
        $array['Texto10'] = config('contacto.provincia');
        $array['Texto11'] = config('contacto.postal');
        $array['Texto4'] = config('contacto.email');
        $array['Texto12'] = Profesor::find(config('contacto.director'))->fullName;
        $array['Grupo1'] = 'Opción1';
        $array['Texto13'] = $fct->Fct->Colaboracion->Ciclo->vliteral;
        $array['Texto14'] = substr($fct->Fct->Colaboracion->Ciclo->Departament->vliteral,12);
        $array['Grupo2'] = $fct->Fct->Colaboracion->Ciclo->tipo == 1?'Opción1':'Opción 2';
        $array['Grupo3'] = 'Opción1';
        $array['Texto15'] = $fct->Fct->Colaboracion->Centro->Empresa->nombre;
        $array['Texto16'] = $fct->Fct->Colaboracion->Centro->Empresa->cif;
        $array['Texto17'] = $fct->Fct->Colaboracion->Centro->Empresa->telefono;
        $array['Texto18'] = $array['Texto17'];
        $array['Texto19'] = $fct->Fct->Colaboracion->Centro->Empresa->email;

        $array['Texto20'] = $fct->Fct->Colaboracion->Centro->Empresa->direccion;
        $array['Texto21'] = $fct->Fct->Colaboracion->Centro->Empresa->localidad;
        $array['Texto22'] = 'Alacant';
        $array['Texto23'] = '';
        $array['Texto24'] = 'Espanya';
        $array['Texto25'] = $fct->Fct->Colaboracion->Centro->direccion;
        $array['Texto26'] = $fct->Fct->Colaboracion->Centro->localidad;
        $array['Texto27'] = 'Alacant';
        $array['Texto28'] = '';
        $array['Texto29'] = 'Espanya';
        $array['Text30'] = $fct->Fct->Colaboracion->telefono;
        $array['Text31'] = $array['Text30'];
        $array['Text32'] = $fct->Fct->Instructor->Nombre;
        $array['Text33'] = $fct->Fct->Instructor->dni;
        $array['Text34'] = $fct->Fct->Instructor->email;
        $array['Text36'] = $array['Texto13'];
        $array['Text39'] = $fct->Alumno->apellido1.' '.$fct->Alumno->apellido2 ;
        $array['Text38'] = $fct->Alumno->nombre;
        $array['Text37'] = $fct->Alumno->dni;
        $array['Grupo5'] = $fct->Alumno->sexo == 'H'?'Opción1':'Opción2';
        $array['Text40'] = $fct->Alumno->fecha_nac;
        $array['Text41'] = $fct->Alumno->domicilio;
        $array['Text42'] = $fct->Alumno->poblacion;
        $array['Text43'] = $fct->Alumno->Provincia->nombre;
        $array['Text44'] = $fct->Alumno->telef1;
        $array['Text45'] = $fct->Alumno->email;
        $array['Text49'] = $fct->desde;
        $array['Text50'] = $fct->hasta;
        //$array['Text51'] = "Programador Web";
        $array['Text47'] = AuthUser()->fullName;
        //$array['Text48'] = "Professor d'educació secundària";
        $array['Casilla de verificación1'] = 'Sí';
        $array['Casilla de verificación2'] = 'Sí';
        $array['Casilla de verificación3'] = 'Sí';
        $array['Casilla de verificación4'] = 'Sí';
        $array['Text52'] = $array['Texto9'];

        $fc1 = new Date();
        Date::setlocale('ca');
        $array['Text53'] = $fc1->format('d');
        $array['Text54'] = $fc1->format('F');
        $array['Text55'] = $fc1->format('Y');
        $array['Text56'] = $array['Texto12'];

        return $array;
    }
    
    
} 